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
\usepackage{graphicx}
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

\sloppy

\usepackage{titlesec}
\defaultfontfeatures{Ligatures=TeX}

\titleformat{\section}[display]
    {\specialfont\Large\bfseries}{\thesection}{0pt}{\Large\MakeUppercase}
\titleformat*{\subsection}{\normalfont\specialfont\large}

\pagestyle{scrheadings}
\ohead{<?php cvtx_kuerzel($post); ?> <?php cvtx_titel($post); ?>}
\setheadsepline{0.4pt}

\titleformat{\section}[display]{\specialfont\Large\bfseries\color{gruen}}{\thesection}{0pt}{\Large\MakeUppercase}
\titleformat*{\subsection}{\normalfont\specialfont\large\color{gruen}}

<?php $options = get_option('cvtx_options'); ?>

\sloppy

\begin{document}

\shorthandoff{"}

\thispagestyle{empty}

\begin{flushright}
 \textbf{\large <?php cvtx_name($post); ?>}\\
 <?php cvtx_beschreibung($post); ?>
\end{flushright}

\noindent\makebox[\linewidth]{\rule{\textwidth}{6pt}}
\noindent
\begin{tabularx}{\textwidth}{@{}lX}
\vspace{16pt}\vspace{-15pt}
\\
    \textbf{\specialfont{\LARGE <?php cvtx_kuerzel($post); ?>}}           &                                              \\
                                                            &                                              \\
    <?php cvtx_print_latex(__('Concerning', 'cvtx')); ?>:   &   <?php cvtx_top($post); ?>                  \\
                                                            &                                              \\
\end{tabularx}
\noindent\makebox[\linewidth]{\rule{\textwidth}{6pt}}

\def\somebox{\vbox{
  \hsize = 5.4cm
  \hspace{0.2cm}
  \vbox{
  	\hsize = 5cm
	\vspace{-1.75cm}
    \begin{small}\begin{flushleft}    
    \noindent\makebox[5cm]{\color{blau}{\rule{5cm}{6pt}}} \smallskip \\
    \vspace{1.75cm}
    \includegraphics[width=1.5cm,keepaspectratio]{<?php cvtx_application_photo($post); ?>}\smallskip \\
    <?php if(isset($options['cvtx_application_gender_check']) && $options['cvtx_application_gender_check']): ?>
        <?php cvtx_application_gender($post, 'magenta'); ?>
    <?php endif; ?>
    <?php cvtx_application_birthdate($post, 'magenta'); ?>
    <?php if (!empty($options['cvtx_application_kvs_name'])) { cvtx_application_kv($post, 'magenta'); ?> \smallskip \\ <?php } ?>
    <?php if (!empty($options['cvtx_application_bvs_name'])) { cvtx_application_bv($post, 'magenta'); ?> \smallskip \\ <?php } ?>
    <?php if (!empty($options['cvtx_application_topics'])) { cvtx_application_topics_latex($post, 'magenta'); ?> \smallskip \\ <?php } ?>
    <?php cvtx_application_website($post, 'magenta'); ?>
    <?php cvtx_application_mail($post, 'magenta'); ?>
    \noindent\makebox[5cm]{\color{blau}{\rule{5cm}{6pt}}}\\
    \vspace{20pt}
    \end{flushleft}\end{small}
}
}}

\section*{<?php cvtx_print_latex(__('Application', 'cvtx')); ?> <?php cvtx_titel($post); ?>}

\InsertBoxR{0}{\somebox}[-2] 

<?php cvtx_text($post); ?>

\subsection*{<?php cvtx_print_latex(__('Biography', 'cvtx')); ?>}
<?php cvtx_application_cv($post); ?>

\end{document}