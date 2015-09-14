\documentclass[paper=a4, 12pt, pagesize, parskip=half, DIV=calc]{scrbook}

\usepackage[left=2.5cm,top=1cm,bottom=0.5cm,right=1.4cm,includeheadfoot]{geometry}

\usepackage[T1]{fontenc}
\usepackage[utf8]{inputenc}
<?php if (get_bloginfo('language') == 'de-DE') { ?>
    \usepackage[ngerman]{babel}
<?php } else { ?>
    \usepackage[english]{babel}
<?php } ?>
\usepackage{fixltx2e}
\usepackage{lineno}
\usepackage{tabularx}
\usepackage[normalem]{ulem}
\usepackage[right]{eurosym}
\usepackage{pdfpages}
\usepackage{calc}
\usepackage{graphicx}
\usepackage{multirow}
\usepackage{hyperref}
\usepackage[strict]{changepage}
\usepackage{scrpage2}

\input{<?php echo CVTX_B90CD_PLUGIN_DIR; ?>tex/packages/insbox/insbox.tex}

\usepackage{xcolor}
\definecolor{gruen}{HTML}{46962B}
\definecolor{dunkelgruen}{HTML}{0A321E}
\definecolor{gelb}{HTML}{FFEE00}
\definecolor{dunkelgelb}{HTML}{FFD500}
\definecolor{blau}{HTML}{4CB4E7}
\definecolor{hellblau}{HTML}{D4EDFC}
\definecolor{magenta}{HTML}{E6007E}
\definecolor{white}{HTML}{FFFFFF}

\usepackage{fontspec}
\newfontfamily\specialfont[%
	ExternalLocation ,
	UprightFont = {<?php echo CVTX_B90CD_PLUGIN_DIR; ?>tex/fonts/Arvo/Arvo-Regular_v104.ttf} ,
	BoldFont = {<?php echo CVTX_B90CD_PLUGIN_DIR; ?>tex/fonts/Arvo/Arvo_Gruen_1004.otf} ]{Arvo}

\setmainfont[%
	ExternalLocation ,
	UprightFont = {<?php echo CVTX_B90CD_PLUGIN_DIR; ?>tex/fonts/PT-Sans/PTS55F.ttf} ,
	BoldFont = {<?php echo CVTX_B90CD_PLUGIN_DIR; ?>tex/fonts/PT-Sans/PTS75F.ttf} ,
	BoldItalicFont = {<?php echo CVTX_B90CD_PLUGIN_DIR; ?>tex/fonts/PT-Sans/PTS76F.ttf} ,
	ItalicFont = {<?php echo CVTX_B90CD_PLUGIN_DIR; ?>tex/fonts/PT-Sans/PTS56F.ttf} ]{PT-Sans}

\renewcommand{\printtoctitle}[1]{\specialfont #1}

\sloppy

\usepackage{titlesec}
\defaultfontfeatures{Ligatures=TeX}

\titleformat{\section}[display]
    {\specialfont\Large\bfseries}{\thesection}{0pt}{\Large\MakeUppercase}
\titleformat*{\subsection}{\normalfont\specialfont\large}

<?php $options = get_option('cvtx_options'); ?>

\sloppy

% Page Style Settings
\pagestyle{scrheadings}
\setheadsepline{0.4pt}
\setuptoc{toc}{totoc}

\newcommand*\adjust{\setlength\hsize{\textwidth-2\tabcolsep}}

% Document Information
\subject{\specialfont{<?php cvtx_name(); ?>\\ <?php cvtx_beschreibung(); ?>}}
\title{\specialfont{<?php cvtx_titel($post); ?>}}
\date{<?php cvtx_print_latex(__('This version', 'cvtx')); ?>: \today}
\author{}

\begin{document}

\shorthandoff{"}

<?php if(cvtx_get_file($post, 'reader_titlepage')): ?>
\includepdf{<?php cvtx_reader_titlepage_file($post); ?>}
\pagestyle{empty}
\newpage
<?php else: ?>
% Show Title Page
\maketitle    
<?php endif; ?>

% Show Table of Contents
\tableofcontents

<?php
$top    = 0;
$antrag = 0;
$i = 0;
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
while ($query->have_posts()) {
    $query->the_post();
    $item = get_post(get_the_ID());
    $i++;
    
    /* Show Resolution */
    if ($item->post_type == 'cvtx_antrag') {
        $antrag = $item->ID;
?>
% Start New Page
\newpage

% Hide Headline and Show Page Number on This Page, Define Headline Text
\thispagestyle{plain}
\ohead{<?php cvtx_kuerzel($item); ?> <?php cvtx_titel($item); ?>}

% Info Box
\noindent\makebox[\linewidth]{\rule{\textwidth}{6pt}}
\noindent
\begin{tabularx}{\textwidth}{@{}lX}
\vspace{16pt}\vspace{-15pt}
\\
    \textbf{\specialfont{\LARGE <?php cvtx_kuerzel($item); ?>}}           &                                              \\
                                                            &                                              \\
    <?php cvtx_print_latex(__('Author(s)', 'cvtx')); ?>:    &   <?php cvtx_antragsteller_kurz($item); ?>   \\
                                                            &                                              \\
    <?php cvtx_print_latex(__('Concerning', 'cvtx')); ?>:   &   <?php cvtx_top($item); ?>                  \\
                                                            &                                              \\
<?php if (cvtx_has_info($item)) { ?>
    <?php cvtx_print_latex(__('Remarks', 'cvtx')); ?>:      &   <?php cvtx_info($item); ?>                 \\
                                                            &                                              \\
<?php } ?>

\end{tabularx}
\noindent\makebox[\linewidth]{\rule{\textwidth}{6pt}}

% Resolution title
\section*{<?php cvtx_titel($item); ?>}

% Add Bookmarks and Reference for Table of Contents
<?php   // Update agenda item if changed
        $this_top = get_post_meta($item->ID, 'cvtx_antrag_top', true);
        if ($top != $this_top) {
            $top  = $this_top;
?>
            \addcontentsline{toc}{chapter}{<?php cvtx_top($item); ?>}
<?php   } ?>
\addcontentsline{toc}{section}{<?php cvtx_kuerzel($item); ?> <?php cvtx_titel($item); ?>}

% Resolution Text
\begin{linenumbers}
\setcounter{linenumber}{1}
<?php cvtx_antragstext($item); ?>
\end{linenumbers}

% Explanation
<?php if (cvtx_has_begruendung($item)) { ?>
   \subsection*{<?php cvtx_print_latex(__('Explanation', 'cvtx')); ?>}
   <?php cvtx_begruendung($item); ?>
<?php } ?>

% Author(s)
\subsection*{<?php cvtx_print_latex(__('Author(s)', 'cvtx')); ?>}
<?php cvtx_antragsteller($item); ?>

<?php
    }
    
    /* Show Application */
    else if ($item->post_type == 'cvtx_application') {
        // Include pdf or load latex file?
        $manually = (get_post_meta($item->ID, 'cvtx_application_manually', true) == 'on');
        
        // Include PDF
        if ($manually && cvtx_get_file($item)) {
?>
% Start New Page
\newpage

% Define Headline Text
\ohead{<?php cvtx_print_latex(__('Application', 'cvtx')); ?> <?php cvtx_kuerzel($item); ?> <?php cvtx_titel($item); ?>}

% Add Bookmarks and Reference for Table of Contents
<?php       // Update agenda item if changed
            $this_top = get_post_meta($item->ID, 'cvtx_application_top', true);
            if ($top != $this_top) {
                $top  = $this_top;
?>
                \addcontentsline{toc}{chapter}{<?php cvtx_top($item); ?>}
<?php       } ?>
\addcontentsline{toc}{section}{<?php cvtx_print_latex(__('Application ', 'cvtx')); ?> <?php cvtx_kuerzel($item); ?> <?php cvtx_titel($item); ?>}

\includepdf[pages=-, pagecommand={\thispagestyle{scrheadings}}, offset=-1.5em 2em, width=1.15\textwidth]{<?php cvtx_application_file($item); ?>}

<?php
        }
        // Show latex inside
        else {
?>
% Start New Page
\newpage

% Define Headline Text
\ohead{<?php cvtx_print_latex(__('Application', 'cvtx')); ?> <?php cvtx_kuerzel($item); ?> <?php cvtx_titel($item); ?>}

\noindent\makebox[\linewidth]{\rule{\textwidth}{6pt}}
\noindent
\begin{tabularx}{\textwidth}{@{}lX}
\vspace{16pt}\vspace{-15pt}
\\
    \textbf{\specialfont{\LARGE <?php cvtx_kuerzel($item); ?>}}           &                                              \\
                                                            &                                              \\
    <?php cvtx_print_latex(__('Concerning', 'cvtx')); ?>:   &   <?php cvtx_top($item); ?>                  \\
                                                            &                                              \\
\end{tabularx}
\noindent\makebox[\linewidth]{\rule{\textwidth}{6pt}}

\def\somebox<?php echo $i; ?>{\vbox{
  \hsize = 5.4cm
  \hspace{0.2cm}
  \vbox{
  	\hsize = 5cm
	\vspace{-1.75cm}
    \begin{small}\begin{flushleft}    
    \noindent\makebox[5cm]{\color{blau}{\rule{5cm}{6pt}}} \smallskip \\
    \vspace{1.75cm}
    \includegraphics[width=1.5cm,keepaspectratio]{<?php cvtx_application_photo($item); ?>}\smallskip \\
    <?php if(isset($options['cvtx_application_gender_check']) && $options['cvtx_application_gender_check']): ?>
        <?php cvtx_application_gender($item, 'magenta'); ?>
    <?php endif; ?>
    <?php cvtx_application_birthdate($item, 'magenta'); ?>
    <?php if (!empty($options['cvtx_application_kvs_name'])) { cvtx_application_kv($item, 'magenta'); ?> \smallskip \\ <?php } ?>
    <?php if (!empty($options['cvtx_application_bvs_name'])) { cvtx_application_bv($item, 'magenta'); ?> \smallskip \\ <?php } ?>
    <?php if (!empty($options['cvtx_application_topics'])) { cvtx_application_topics_latex($item, 'magenta'); ?> \smallskip \\ <?php } ?>
    <?php cvtx_application_website($item, 'magenta'); ?>
    <?php cvtx_application_mail($item, 'magenta'); ?>
    \noindent\makebox[5cm]{\color{blau}{\rule{5cm}{6pt}}}\\
    \vspace{20pt}
    \end{flushleft}\end{small}
}
}}

\section*{<?php cvtx_print_latex(__('Application', 'cvtx')); ?> <?php cvtx_titel($item); ?>}

% Add Bookmarks and Reference for Table of Contents
<?php   // Update agenda item if changed
        $this_top = get_post_meta($item->ID, 'cvtx_application_top', true);
        if ($top != $this_top) {
            $top  = $this_top;
?>
            \addcontentsline{toc}{chapter}{<?php cvtx_top($item); ?>}
<?php   } ?>
\addcontentsline{toc}{section}{<?php cvtx_print_latex(__('Application ', 'cvtx')); ?> <?php cvtx_kuerzel($item); ?> <?php cvtx_titel($item); ?>}

\InsertBoxR{0}{\somebox<?php echo $i; ?>}[-2] 

<?php cvtx_text($item); ?>

\subsection*{<?php cvtx_print_latex(__('Biography', 'cvtx')); ?>}
<?php cvtx_application_cv($item); ?>

<?php
        }
    }

    /* Show Amendment */
    else if ($item->post_type == 'cvtx_aeantrag') {
?>
% Start New Page
\newpage
% Hide Headline and Show Page Number on This Page, Define Headline Text
\thispagestyle{plain}
\ohead{<?php cvtx_print_latex(__('Amendment', 'cvtx')); ?> <?php cvtx_kuerzel($item); ?>}

% Site Title and Subtitle
\begin{flushright}
 \textbf{\large <?php cvtx_name(); ?>}\\
 <?php cvtx_beschreibung(); ?>
\end{flushright}

% Info Box
\noindent\makebox[\linewidth]{\rule{\textwidth}{6pt}}
\noindent
\begin{tabularx}{\textwidth}{@{}lX}
\vspace{16pt}\vspace{-15pt}
\\
                                                            &                                                                     \\
    \multicolumn{2}{>{\adjust}X}{\textbf{\LARGE <?php cvtx_kuerzel($item); ?>}}                                                 \\
                                                            &                                                                     \\
    <?php cvtx_print_latex(__('Author(s)', 'cvtx')); ?>:    &   <?php cvtx_antragsteller_kurz($item); ?>                          \\
                                                            &                                                                     \\
    <?php cvtx_print_latex(__('Concerning', 'cvtx')); ?>:   &   <?php cvtx_antrag($item); ?> (<?php cvtx_top_titel($item); ?>)    \\
                                                            &                                                                     \\
<?php if (cvtx_has_info($item)) { ?>
    <?php cvtx_print_latex(__('Remarks', 'cvtx')); ?>:      &   <?php cvtx_info($item); ?>                                        \\
                                                            &                                                                     \\
<?php } ?>
\end{tabularx}
\noindent\makebox[\linewidth]{\rule{\textwidth}{6pt}}

% Amendment Title
\section*{<?php cvtx_print_latex(__('Amendment', 'cvtx')); ?> <?php cvtx_kuerzel($item); ?>}

% Add Bookmarks and Reference for Table of Contents
<?php   // Update agenda item if changed
        $this_antrag = get_post_meta($item->ID, 'cvtx_aeantrag_antrag', true);
        $this_top    = get_post_meta($this_antrag, 'cvtx_antrag_top', true);
        if ($top != $this_top) {
            $top  = $this_top;
?>
            \addcontentsline{toc}{chapter}{<?php cvtx_top($item); ?>}
<?php   }
        // Update resolution if changed
        if ($antrag != $this_antrag) {
            $antrag  = $this_antrag;
?>
            \addcontentsline{toc}{section}{<?php cvtx_antrag($item); ?>}
<?php   } ?>
\addcontentsline{toc}{subsection}{<?php cvtx_print_latex(__('Amendment', 'cvtx')); ?> <?php cvtx_kuerzel($item); ?>}

% Amendment Text
\begin{linenumbers}
\setcounter{linenumber}{1}
<?php cvtx_antragstext($item); ?>
\end{linenumbers}

% Explanation
<?php if (cvtx_has_begruendung($item)) { ?>
    \subsection*{<?php cvtx_print_latex(__('Explanation', 'cvtx')); ?>}
    <?php cvtx_begruendung($item); ?>
<?php } ?>

% Author(s)
\subsection*{<?php cvtx_print_latex(__('Author(s)', 'cvtx')); ?>}
<?php cvtx_antragsteller($item); ?>


<?php
    }
}
?>

\end{document}
