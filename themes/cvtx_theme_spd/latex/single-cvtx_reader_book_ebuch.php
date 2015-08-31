\documentclass[paper=a4,landscape, twoside=false,fontsize=9pt, twocolumn]{scrartcl}

\usepackage[left=1.5cm,top=2cm,bottom=2cm,right=1.5cm]{geometry}
\setlength{\footskip}{30pt}

<?php if (get_bloginfo('language') == 'de-DE') { ?>
    \usepackage[ngerman]{babel}
<?php } else { ?>
    \usepackage[english]{babel}
<?php } ?>

\usepackage{fixltx2e}
\usepackage[normalem]{ulem}
\usepackage[right]{eurosym}
\usepackage{pdfpages}

\sloppy

\usepackage{fontspec}
\setmainfont[%
	ExternalLocation ,
	UprightFont = {<?php print get_home_path(); ?>fonts/thesans-lp5plain.ttf} ,
	BoldFont = {<?php print get_home_path(); ?>fonts/thesans-lp7bld.ttf} ,
	BoldItalicFont = {<?php print get_home_path(); ?>fonts/thesans-bold-italic.ttf} ,
	ItalicFont = {<?php print get_home_path(); ?>fonts/thesans-b5-plain-italic.ttf} ]{TheSans}
\renewcommand{\baselinestretch}{1.1} 

\usepackage{enumitem}
\setlist{rightmargin=7pt}

\usepackage[parfill]{parskip}

\setkomafont{pagehead}{\normalfont\normalcolor}
\usepackage{scrpage2}
\pagestyle{scrheadings}
\ihead{\uppercase{\textbf{<?php cvtx_name(); ?> <?php cvtx_beschreibung(); ?>}}}
\setheadsepline{0pt}

\cfoot{\fontsize{7}{9}Seite \thepage}

\usepackage{xcolor}

\definecolor{rot}{HTML}{E3000F}
\definecolor{lila}{HTML}{980065}
\definecolor{grau}{HTML}{999999}
\definecolor{lightgray}{gray}{0.9}

\usepackage{tabularx}
\usepackage{booktabs}

\usepackage{titlesec}
\titleformat{\subsection}[hang]{\bfseries}{}{0pt}{}[]
\titlespacing*{\subsection}{7pt}{0pt}{0pt}

\usepackage{tocloft}

\usepackage{tikz}
\usetikzlibrary{backgrounds}
\usetikzlibrary{calc}

\newcounter{seccntr}
\setcounter{seccntr}{-1}

\newcommand*{\hnode}[1]{%
    \tikz[remember picture] \node[minimum size=0pt,inner sep=0pt,outer sep=4.5pt] (#1) {};}
% create a node at the beginning of the section entry
\renewcommand{\cftsecfont}{\hnode{P1}\bfseries\Large
        \tikz[remember picture,overlay] \draw (P1.north west)  [line width={17pt}, gray,opacity=0.3] -- ++($(\textwidth,0) + (1ex,0)$);%----- 0 --
}
\renewcommand{\cftsecpagefont}{\bfseries}

\titleformat{\section}[hang]
  {\bfseries\large}{}{0pt}{\colorsection}[]
\titlespacing*{\section}{0pt}{\baselineskip}{\baselineskip}

\newcommand{\colorsection}[1]{%
  \colorbox{gray!30}{\parbox{\dimexpr\textwidth-2\fboxsep}{#1}}}


\setlength{\cftbeforesubsecskip}{8pt}

\begin{document}

<?php if (get_bloginfo('language') == 'de-DE') { ?>
    \shorthandoff{"}
<?php } ?>

<?php if(cvtx_get_file($post, 'reader_titlepage')): ?>
\includepdf{<?php cvtx_reader_titlepage_file($post); ?>}
\newpage
<?php endif; ?>

<?php if(get_post_meta($post->ID, 'cvtx_reader_page_start', true)): ?>
\setcounter{page}{<?php print get_post_meta($post->ID, 'cvtx_reader_page_start', true); ?>}
<?php endif; ?>

<?php
$top    = 0;
$antrag = 0;
$query  = new WP_Query(array('post_type'   => array('cvtx_antrag',
                                                    'cvtx_aeantrag',
                                                    'cvtx_application'),
                             'taxonomy'    => 'cvtx_tax_reader',
                             'term'        => 'cvtx_reader_'.intval($post->ID),
                             'orderby'     => 'meta_value',
                             'meta_key'    => 'cvtx_sort',
                             'order'       => 'ASC',
                             'nopaging'    => true,
                             'post_status' =>'publish'));
$running_antraege = false;
$running_aeantraege = false;
$prev_antrag = false;
$prev_aeantrag = false;
while ($query->have_posts()) {
    $query->the_post();
    $item = get_post(get_the_ID());
    if(!cvtx_spd_part_of_ebuch($item))
      continue;
?>      
<?php
    /* Show Resolution */
    if ($item->post_type == 'cvtx_antrag') { ?>
    <?php $antrag = $item->ID; ?>

<?php   // Update agenda item if changed
        $this_top    = get_post_meta($antrag, 'cvtx_antrag_top', true);
        if ($top != $this_top) {
            $top  = $this_top;
?>
<?php } else if($running_aeantraege) {?>
<?php } ?>

\begingroup\setlength{\fboxsep}{0pt}
\colorbox{lightgray}{%
\begin{tabularx}{\linewidth}{l l}
\textbf{<?php cvtx_kuerzel($item); ?>} & \textbf{<?php cvtx_antragsteller($item); ?>} \\
\multicolumn{2}{l}{\textbf{<?php cvtx_titel($item); ?>}}\\
\bottomrule
\end{tabularx}}\endgroup
\vspace{0.5cm}
<?php cvtx_spd_tex_beschluss($item); ?>
\newline\newline
\textbf{<?php cvtx_spd_answer_rep($item); ?>}
\begin{quote}
<?php cvtx_spd_answer($item); ?>
\end{quote}
\vspace{0.5cm}
<?php
    }

    /* Show Amendment */
    else if ($item->post_type == 'cvtx_aeantrag') {
?>

% Add Bookmarks and Reference for Table of Contents
<?php   // Update agenda item if changed
        $this_antrag = get_post_meta($item->ID, 'cvtx_aeantrag_antrag', true);
        $this_top    = get_post_meta($this_antrag, 'cvtx_antrag_top', true);
        if ($top != $this_top) {
            $top  = $this_top;
        }
        // Update resolution if changed
        if ($antrag != $this_antrag || !$running_aeantraege) {
            $antrag  = $this_antrag;
        } 
?>

\begingroup\setlength{\fboxsep}{0pt}
\colorbox{lightgray}{%
\begin{tabularx}{\linewidth}{l l}
\textbf{<?php cvtx_kuerzel($item); ?>} & \textbf{<?php cvtx_antragsteller($item); ?>} \\
\bottomrule
\end{tabularx}}\endgroup
\vspace{0.5cm}
<?php cvtx_spd_tex_beschluss($item); ?>
\newline\newline
\textbf{<?php cvtx_spd_answer_rep($item); ?>}
\begin{quote}
<?php cvtx_spd_answer($item); ?>
\end{quote}
\vspace{0.5cm}

<?php
    }
}
?>
\end{document}