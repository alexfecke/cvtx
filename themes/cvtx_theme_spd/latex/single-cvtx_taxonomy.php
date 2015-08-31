\documentclass[paper=a4, twoside=false,fontsize=9pt]{scrartcl}

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

\newcommand*{\logoimg}[1]{%
  \raisebox{-.1\baselineskip}{%
    \includegraphics[
      height=2\baselineskip,
      width=2\baselineskip,
      keepaspectratio,
    ]{#1}%
  }%
}

\setkomafont{pagehead}{\normalfont\normalcolor}
\usepackage{scrpage2}
\pagestyle{scrheadings}
%\chead{\hspace{1.2cm} \uppercase{\textbf{<?php cvtx_name(); ?> <?php cvtx_beschreibung(false, $event_id); ?>}}}
\chead{\uppercase{\textbf{<?php cvtx_name(); ?>}} \hspace{0.2cm} \logoimg{<?php print get_home_path(); ?>tex-source/spd_logo_jpg-data} \hspace{0.2cm} \uppercase{\textbf{<?php cvtx_beschreibung(false, $event_id); ?>}}}
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
        \tikz[remember picture,overlay] \draw (P1.north west)  [line width={17pt}, gray,opacity=0.3] -- ++($(\linewidth,0) + (1ex,0)$);%----- 0 --
}
\renewcommand{\cftsecpagefont}{\bfseries}

\titleformat{\section}[hang]
  {\bfseries\large}{}{0pt}{\colorsection}[]
\titlespacing*{\section}{0pt}{\baselineskip}{\baselineskip}

\newcommand{\colorsection}[1]{%
  \colorbox{gray!30}{\parbox{\dimexpr\linewidth-2\fboxsep}{#1}}}


\setlength{\cftbeforesubsecskip}{8pt}


\title{\"Uberwiesen an: <?php echo $post->name; ?>}
\subtitle{Veranstaltung: <?php echo get_the_title($event_id); ?>}

\begin{document}

\maketitle

% Show Table of Contents
%\tableofcontents
%\newpage

<?php if (get_bloginfo('language') == 'de-DE') { ?>
    \shorthandoff{"}
<?php } ?>

<?php
$top    = 0;
$antrag = 0;
$query  = new WP_Query(array('post_type'   => array('cvtx_antrag',
                                                    'cvtx_aeantrag',
                                                    'cvtx_application'),
                             'meta_query' => array(
                                array(
                                  'key' => 'cvtx_antrag_event',
                                  'value' => $event_id,
                                  'compare' => '=',
                                ),
                             ),
                             'taxonomy'    => 'cvtx_tax_assign_to',
                             'term'        => $post->slug,
                             'orderby'     => 'meta_value',
                             'meta_key'    => 'cvtx_sort',
                             'order'       => 'ASC',
                             'nopaging'    => true,
                             'post_status' =>'publish'));
$running_antraege = false;
$running_aeantraege = false;
$prev_antrag = false;
$prev_aeantrag = false;
if(!$query->have_posts()) {
  print 'Keine Antr\"age vorhanden.';
}
while ($query->have_posts()) {
    $query->the_post();
    $item = get_post(get_the_ID());
?>      
<?php
    /* Show Resolution */
    if ($item->post_type == 'cvtx_antrag') { ?>
    <?php $antrag = $item->ID; ?>
\newpage
<?php   // Update agenda item if changed
        $this_top    = get_post_meta($antrag, 'cvtx_antrag_top', true);
        if ($top != $this_top) {
            $top  = $this_top;
?>
\addcontentsline{toc}{section}{<?php cvtx_top($item); ?>}
\section*{<?php cvtx_top($item); ?>}
<?php } else if($running_aeantraege) {?>
<?php } ?>
\addcontentsline{toc}{subsection}{<?php cvtx_titel($item); ?>}

\begingroup\setlength{\fboxsep}{0pt}
\colorbox{lightgray}{%
\begin{tabularx}{\linewidth}{l l}
\textbf{<?php cvtx_kuerzel($item); ?>} & \textbf{<?php cvtx_antragsteller($item); ?>} \\
\multicolumn{2}{p{0.9\linewidth}}{\textbf{<?php cvtx_titel($item); ?>}}\\
\bottomrule
\end{tabularx}}\endgroup
\vspace{0.5cm}
<?php cvtx_spd_tex_recipient($item); ?>
\newline
<?php cvtx_spd_tex_beschluss($item, false); ?>
\par
\textbf{<?php cvtx_spd_antrag_expl($item); ?>}
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
?>
        \addcontentsline{toc}{section}{<?php cvtx_top($item); ?>}
        \section*{<?php cvtx_top($item); ?>}
<?php
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
<?php cvtx_spd_tex_beschluss($item, false); ?>
\vspace{0.5cm}

<?php
    }
}
?>
\end{document}