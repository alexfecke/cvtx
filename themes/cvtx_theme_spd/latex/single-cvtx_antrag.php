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

<?php
$options = get_option('cvtx_spd_options');
$col1 = isset($options['cvtx_spd_pdf_columns']);
?>

\usepackage[pagewise]{lineno}
\renewcommand\linenumberfont{\normalfont\small}

\usepackage{lipsum}

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
  \raisebox{2.\baselineskip}{%
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
<?php if(!$col1): ?>
\ihead{\hspace{1.2cm} \uppercase{\textbf{<?php cvtx_name(); ?> <?php cvtx_beschreibung(); ?>}}}
<?php else: ?>
\setlength{\headheight}{6\baselineskip}
\ihead{\newline\newline\newline\uppercase{\textbf{<?php cvtx_name(); ?>}}\\\uppercase{\textbf{<?php cvtx_beschreibung(); ?>}}}
\chead{\logoimg{<?php print get_home_path(); ?>tex-source/spd_logo_jpg-data}}
<?php endif; ?>
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

<?php if(!$col1): ?>
% prevent latex from typesetting into bigger lines
\lineskiplimit=-100pt\relax
\setlength{\parskip}{0pt}

\makeatletter

\let\oldDispatch=\cals@row@dispatch
\newbox\rowBefore
\newbox\rowAfter
\newbox\rowIntermediate
\newbox\decorationCopy
\newdimen\splitHeight

\def\cals@row@dispatch{%
\cals@ifbreak\iftrue % detect that a break is required

  \splitHeight=\pagegoal \advance\splitHeight -\pagetotal
  
  \ifdim \splitHeight>50pt % break inrow only if at least 100pt left
    \advance\splitHeight -15pt % avoid pagebreak due to overflows
    %
    % Split the current row on two: before and after the break
    %
    \setbox\rowBefore=\hbox{}
    \setbox\rowAfter=\hbox{}
    \def\next{%
      \setbox0=\lastbox
%      \showthe\currentVLine
%      \setbox3=\lastbox
      \ifvoid0
        \def\next{\global\setbox\rowBefore=\box\rowBefore
                  \global\setbox\rowAfter=\box\rowAfter }%
      \else
        \setbox2=\vsplit0 to\splitHeight
        \ifvoid0
%          \setbox0=\copy2\relax
%		\setbox0=\hbox{}
        \fi
        \setbox\rowBefore=\hbox{\box2 \unhbox\rowBefore}%
        \setbox\rowAfter=\hbox{\box0 \unhbox\rowAfter}%
      \fi
      \next}
    \setbox0=\hbox{\unhbox\cals@current@row \next}
%    \setbox3=\hbox{\unhbox\currentVLine \next}
    %
    % Decoration backup, typeset the first row,
    % restore context, typeset the second at the end of macro
    %

    \setbox\decorationCopy=\copy\cals@current@cs
    \setbox\cals@current@row=\box\rowBefore
    \ht\cals@current@cs=\ht\cals@current@row
    \oldDispatch
    \cals@issue@break

%    \setcounter{myLN}{1}

    \cals@thead@tokens

    \setbox\rowIntermediate=\hbox{}
    \setbox\cals@current@row=\box\rowIntermediate
    \ht\cals@current@cs=\ht\cals@current@row
    \oldDispatch
        
    \setbox\cals@current@row=\box\rowAfter
    \cals@reheight@cells\cals@current@row
    \setbox\cals@current@cs=\box\decorationCopy
    \let\cals@current@context=b
    \let\cals@last@context=b
    \ht\cals@current@cs=\ht\cals@current@row
    
    \cals@ifbreak\iftrue
    	\cals@row@dispatch
    \fi
  \fi
\fi
\oldDispatch
}

\newcounter{numberLines}
\setcounter{numberLines}{0}

\newcounter{maxNum}
\setcounter{maxNum}{0}

\newcommand\myLN{ 
     \let\@@par\mypar
     \ifx\@par\@@@par\let\@par\mypar\fi
     \ifx\par\@@@par\let\par\mypar\fi
}

\newcommand\mypar{% 
     \ifvmode\@@@par\else\ifinner\@@@par\else\@@@par
          \addtocounter{numberLines}{\prevgraf}
     \fi\fi
     }

\newcommand*\ifcounter[1]{%
  \ifcsname c@#1\endcsname
    \expandafter\@firstoftwo
  \else
    \expandafter\@secondoftwo
  \fi
}

\def\cals@rs@width{0pt}%
%\def\cals@cs@width{0pt}

\IfFileExists{\jobname.linenumbers}{\input{\jobname.linenumbers}}{}

\usepackage{newfile}
\newoutputstream{linenumbers}
\openoutputfile{\jobname.linenumbers}{linenumbers}
\newcounter{rows}
\setcounter{rows}{0}
\newcounter{cell1}
\newcounter{cell2}
\newcounter{looper}
\setcounter{looper}{0}
\setcounter{cell1}{0}
\setcounter{cell2}{0}
\newcounter{myLN}
\setcounter{myLN}{1}
<?php endif; ?>

\setlength{\cftbeforesubsecskip}{8pt}

\begin{document}

<?php if (get_bloginfo('language') == 'de-DE') { ?>
    \shorthandoff{"}
<?php } ?>

<?php if(!$col1) : ?>
\begin{calstable} 
\colwidths{{1cm}{8cm}{8cm}}
\cals@paddingL=7pt
\cals@paddingR=7pt
\cals@paddingB=0pt
\cals@paddingT=0pt
\def\cals@cs@width{0pt}

\brow \erow
\brow
\addtocounter{rows}{1}
\alignR\cell{
%\the\value{rows1}
\newcounter{lastPage}
\setcounter{lastPage}{1}
\newcounter{mycurrentPage}
\setcounter{mycurrentPage}{1}
\newcounter{loopTo}
\newcounter{printLN}
\setcounter{printLN}{1}
\ifcounter{row\the\value{rows}}{\setcounter{loopTo}{\the\value{row\the\value{rows}}}\addtocounter{loopTo}{1}}{\setcounter{loopTo}{1}}
\loop\ifnum\value{myLN}<\the\value{loopTo}
\label{linenumber-row\the\value{rows}-line\the\value{myLN}}
\ifnum\getpagerefnumber{linenumber-row\the\value{rows}-line\the\value{myLN}}=\value{lastPage}
\else
\setcounter{printLN}{1}
\addtocounter{lastPage}{1}
\fi
\the\value{printLN}
\\
\addtocounter{myLN}{1}
\addtocounter{printLN}{1}
\repeat
}
\def\cals@borderR{0.4pt}
<?php endif; ?>
\addcontentsline{toc}{subsection}{\protect \hspace{2.3em} \textbf{<?php cvtx_kuerzel($post); ?>} \hfill \textbf{<?php cvtx_antragsteller($post); ?>} \\ <?php cvtx_titel($post); ?> <?php if(cvtx_spd_has_ak_recommendation($post)): ?> \\ \textit{<?php cvtx_spd_ak_recommendation($post); ?>} <?php endif; ?>}
<?php if(!$col1): ?>
\alignL\cell{
\begin{myLN}
<?php else: ?>
\begin{linenumbers}
<?php endif; ?>
\textbf{<?php cvtx_kuerzel($post); ?>}\\
\textbf{<?php cvtx_antragsteller($post); ?>}\\
<?php cvtx_spd_tex_recipient($post); ?>
\par
\textbf{<?php cvtx_titel($post); ?>}\\
<?php if(cvtx_spd_antrag_is_decided($post->ID)): ?>
<?php cvtx_spd_tex_beschluss1($post); ?>
<?php else: ?>
<?php cvtx_antragstext($post); ?>
<?php endif; ?>
<?php if (cvtx_spd_has_begruendung($post)) { ?>
\newline\newline
    \textbf{<?php cvtx_print_latex(__('Explanation', 'cvtx')); ?>}\\
    <?php cvtx_begruendung($post); ?>
<?php } ?>
\par
<?php if(!$col1): ?>
\end{myLN}
\setcounter{maxNum}{\value{numberLines}}
\setcounter{cell1}{\value{numberLines}}
\ifcounter{row\the\value{rows}}{
\ifnum\value{cell1}<\value{row\the\value{rows}}
\setcounter{looper}{\value{cell1}}
~\loop\ifnum\value{looper}<\value{row\the\value{rows}}
\newline
\addtocounter{looper}{1}
\repeat
\fi
}{}
\setcounter{numberLines}{0}
}
\def\cals@borderR{0pt}
  \cell{\begin{myLN}
<?php else: ?>
\end{linenumbers}
\vspace{1cm}
\begin{linenumbers}
<?php endif; ?>
<?php if(cvtx_spd_antrag_is_decided($post->ID)): ?>
\textbf{<?php cvtx_spd_antrag_expl($post); ?>}
<?php elseif (cvtx_spd_has_ak_recommendation($post)): ?>
\textbf{<?php cvtx_spd_ak_recommendation($post); ?>}\\
\newline
<?php endif; ?>
<?php if(!cvtx_spd_antrag_is_decided($post->ID)): ?>
<?php cvtx_spd_version_ak($post); ?>
<?php endif; ?>
\par
<?php if(!$col1): ?>
\end{myLN}
\setcounter{cell2}{\value{numberLines}}
\ifnum\value{numberLines} > \value{maxNum}
\setcounter{maxNum}{\value{numberLines}}
\fi
\addtostream{linenumbers}{\protect\newcounter{row\the\value{rows}}\protect\setcounter{row\the\value{rows}}{\the\value{maxNum}}}
\ifcounter{row\the\value{rows}}{
\ifnum\value{cell2}<\value{row\the\value{rows}}
\setcounter{looper}{\value{cell2}}
~\loop\ifnum\value{looper}<\value{row\the\value{rows}}
\newline
\addtocounter{looper}{1}
\repeat
\fi
}{}
\setcounter{numberLines}{0}
}
\erow
\end{calstable}
\closeoutputstream{linenumbers}
<?php else: ?>
\end{linenumbers}
<?php endif; ?>

\end{document}