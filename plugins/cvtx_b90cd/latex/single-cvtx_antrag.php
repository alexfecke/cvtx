\documentclass[paper=a4, 12pt, pagesize, parskip=half, DIV=calc]{scrartcl}

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
\usepackage[strict]{changepage}
\usepackage{scrpage2}

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

\sloppy

\usepackage{titlesec}
\defaultfontfeatures{Ligatures=TeX}

\titleformat{\section}[display]
    {\specialfont\Large\bfseries}{\thesection}{0pt}{\Large\MakeUppercase}
\titleformat*{\subsection}{\normalfont\specialfont\large}

\pagestyle{scrheadings}
\ohead{<?php cvtx_kuerzel($post); ?> <?php cvtx_titel($post); ?>}
\setheadsepline{0.4pt}

\newcommand*\adjust{\setlength\hsize{\textwidth-2\tabcolsep}}

\begin{document}\thispagestyle{empty}

<?php if (get_bloginfo('language') == 'de-DE') { ?>
    \shorthandoff{"}
<?php } ?>

\begin{flushright}
 \specialfont{\textbf{\large <?php cvtx_name($post); ?>}\\
 <?php cvtx_beschreibung($post); ?>}
\end{flushright}

\noindent\makebox[\linewidth]{\rule{\textwidth}{6pt}}
\noindent
\begin{tabularx}{\textwidth}{@{}lX}
\vspace{16pt}\vspace{-15pt}
\\
    \textbf{\specialfont{\LARGE <?php cvtx_kuerzel($post); ?>}}           &                                              \\
                                                            &                                              \\
    <?php cvtx_print_latex(__('Author(s)', 'cvtx')); ?>:    &   <?php cvtx_antragsteller_kurz($post); ?>   \\
                                                            &                                              \\
    <?php cvtx_print_latex(__('Concerning', 'cvtx')); ?>:   &   <?php cvtx_top($post); ?>                  \\
                                                            &                                              \\
<?php if (cvtx_has_info($post)) { ?>
    <?php cvtx_print_latex(__('Remarks', 'cvtx')); ?>:      &   <?php cvtx_info($post); ?>                 \\
                                                            &                                              \\
<?php } ?>

\end{tabularx}
\noindent\makebox[\linewidth]{\rule{\textwidth}{6pt}}

\section*{<?php cvtx_titel($post); ?>}

\begin{linenumbers}
<?php cvtx_antragstext($post); ?>
\end{linenumbers}

<?php if (cvtx_has_begruendung($post)) { ?>
    \subsection*{<?php cvtx_print_latex(__('Explanation', 'cvtx')); ?>}
    <?php cvtx_begruendung($post); ?>
<?php } ?>

\subsection*{<?php cvtx_print_latex(__('Author(s)', 'cvtx')); ?>}
<?php cvtx_antragsteller($post); ?>

\end{document}
