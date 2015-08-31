\documentclass[paper=a4,twoside=false,fontsize=9pt]{scrartcl}

\usepackage[left=2cm,top=2cm,bottom=2cm,right=2cm]{geometry}
\setlength{\footskip}{30pt}
<?php if (get_bloginfo('language') == 'de-DE') { ?>
    \usepackage[ngerman]{babel}
<?php } else { ?>
    \usepackage[english]{babel}
<?php } ?>
\usepackage{zref-abspage}
\usepackage{zref-user}
\usepackage{atbegshi}
\usepackage{refcount}

\usepackage{fixltx2e}
\usepackage[normalem]{ulem}
\usepackage[right]{eurosym}
\usepackage{pdfpages}

\sloppy

\usepackage{ifthen}

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

\usepackage{epstopdf}

\newcommand*{\logoimg}[1]{%
  \raisebox{2.\baselineskip}{%
    \includegraphics[
      height=2\baselineskip,
      width=2\baselineskip,
      keepaspectratio,
    ]{#1}%
  }%
}

\setlength{\headheight}{6\baselineskip}
\setkomafont{pagehead}{\normalfont\normalcolor}
\usepackage{scrpage2}
\pagestyle{scrheadings}
\ihead{\newline\newline\newline\uppercase{\textbf{<?php cvtx_name(); ?>}}\\\uppercase{\textbf{<?php cvtx_beschreibung(); ?>}}}
\chead{\logoimg{<?php print get_home_path(); ?>tex-source/spd_logo_jpg-data}}
\setheadsepline{0pt}

\cfoot{\fontsize{7}{9}Seite \thepage}

\usepackage{xcolor}

\definecolor{rot}{HTML}{E3000F}
\definecolor{lila}{HTML}{980065}
\definecolor{grau}{HTML}{999999}
 
\usepackage{titlesec}
\titleformat{\subsection}[hang]{\bfseries}{}{0pt}{}[]
\titlespacing*{\subsection}{7pt}{0pt}{0pt}

\usepackage{tocloft}

\usepackage{pdfcolparallel}

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

\usepackage{<?php print get_home_path(); ?>cals/cals}
\showboxbreadth=100
\showboxdepth=100

\setlength{\cftbeforesubsecskip}{8pt}

\begin{document}

<?php if (get_bloginfo('language') == 'de-DE') { ?>
    \shorthandoff{"}
<?php } ?>

\addcontentsline{toc}{subsection}{\protect \hspace{2.3em} \textbf{<?php cvtx_kuerzel($post); ?>} \hfill \textbf{<?php cvtx_antragsteller($post); ?>} \\ <?php cvtx_titel($post); ?> <?php if(cvtx_spd_has_ak_recommendation($post)): ?> \\ \textit{<?php cvtx_spd_ak_recommendation($post); ?>} <?php endif; ?>}

\begin{Large}\textbf{<?php cvtx_kuerzel($post); ?>}\end{Large}\\
\newline
\begin{Large}\textbf{Beschluss}\end{Large}\\
\newline
\textbf{<?php cvtx_spd_antrag_expl($post); ?>}
\\
\textbf{<?php cvtx_antragsteller($post); ?>}\\
<?php cvtx_spd_tex_recipient($post); ?>
\par
\begin{Large}\textbf{<?php cvtx_titel($post); ?>}\end{Large}\\
\newline
<?php cvtx_spd_tex_beschluss1($post, false); ?>
\\
\vspace{0.5cm}

%\begin{Large}\textbf{<?php cvtx_spd_answer_rep($post); ?>}\end{Large}\\
\begin{quote}
<?php cvtx_spd_answer($post); ?>
\end{quote}
\vspace{0.5cm}

\end{document}