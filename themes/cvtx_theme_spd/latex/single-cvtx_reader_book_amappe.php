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

\usepackage{hyperref}

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

<?php if(!$col1): ?>

\usepackage{<?php print get_home_path(); ?>cals/cals}
\showboxbreadth=100
\showboxdepth=100


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
\newdimen\splitH

\def\cals@row@dispatch{%
\cals@ifbreak\iftrue % detect that a break is required

  \splitHeight=\pagegoal \advance\splitHeight -\pagetotal
  
  \ifdim \splitHeight>0pt % break inrow only if at least 100pt left
    \advance\splitHeight -5pt % avoid pagebreak due to overflows
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
\newcounter{lastPage}
\setcounter{lastPage}{1}
\newcounter{printLN}
\setcounter{printLN}{1}
\newcounter{loopTo}
\newboolean{madeNewPage}
\setboolean{madeNewPage}{false}

<?php endif; ?>

\setlength{\cftbeforesubsecskip}{8pt}

\begin{document}
\pagenumbering{gobble}

<?php if (get_bloginfo('language') == 'de-DE') { ?>
    \shorthandoff{"}
<?php } ?>

<?php if(cvtx_get_file($post, 'reader_titlepage')): ?>
\includepdf{<?php cvtx_reader_titlepage_file($post); ?>}
\pagestyle{empty}
\newpage
<?php endif; ?>

% Show Table of Contents
\tableofcontents

\cleardoublepage
\pagestyle{scrheadings}
\pagenumbering{arabic}
<?php if(get_post_meta($post->ID, 'cvtx_reader_page_start', true)): ?>
\setcounter{page}{<?php print get_post_meta($post->ID, 'cvtx_reader_page_start', true); ?>}
<?php endif; ?>
\setcounter{lastPage}{\thepage}      

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
                             //'post_status' =>'publish'
                             ));
$running_antraege = false;
$running_aeantraege = false;
$prev_antrag = false;
$prev_aeantrag = false;
$after_toc = true;
while ($query->have_posts()) {
    $query->the_post();
    $item = get_post(get_the_ID());
?>
\setboolean{madeNewPage}{false}
<?php if(!$col1): ?>
\splitH=\pagegoal \advance\splitH-\pagetotal
\ifdim \splitH<150pt
\newpage
\setboolean{madeNewPage}{true}
\fi

\vspace{0.5cm}
\hrule width \textwidth height 0.4pt depth 0pt\relax
\vspace{0.5cm}
<?php endif; ?>
<?php
    /* Show Resolution */
    if ($item->post_type == 'cvtx_antrag') { ?>
    <?php if($running_aeantraege): ?>
    \end{calstable}
    <?php endif; ?>

% Add Bookmarks and Reference for Table of Contents
<?php   // Update agenda item if changed
        $this_top    = get_post_meta($item->ID, 'cvtx_antrag_top', true);
        if ($top != $this_top) {
            $top  = $this_top;
            $prev_antrag = false;
            $prev_aeantrag = false;
?>
<?php if(!$after_toc): ?>
\ifthenelse{\boolean{madeNewPage}}{}{\newpage}
<?php else: $after_toc = false; ?>
<?php endif; ?>
        \addcontentsline{toc}{section}{<?php cvtx_top($item); ?>}
        \section*{<?php cvtx_top($item); ?>}
<?php } else if($running_aeantraege) {?>
\vspace{1cm}
<?php } ?>

<?php if($running_aeantraege): $prev_aeantrag = false; $running_aeantraege = false; endif; ?>

<?php if(!$running_antraege && !$col1) : ?>
\begin{calstable} 
\colwidths{{1cm}{8cm}{8cm}}
\cals@paddingL=7pt
\cals@paddingR=7pt
\cals@paddingB=0pt
\cals@paddingT=7pt
\def\cals@cs@width{0pt}

\brow \erow
<?php $running_antraege = true; endif; ?>

<?php $antrag = $item->ID; ?>
<?php if(!$col1): ?>
\brow
\addtocounter{rows}{1}
\alignR\cell{
%\the\value{rows1}
\ifcounter{row\the\value{rows}}{\setcounter{loopTo}{\the\value{row\the\value{rows}}}\addtocounter{loopTo}{1}}{\setcounter{loopTo}{1}}
\setcounter{myLN}{1}
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

\alignL\cell{
\begin{myLN}
<?php else: ?>
\begin{linenumbers}
<?php endif; ?>
\addcontentsline{toc}{subsection}{\protect \hspace{2.3em} \textbf{<?php cvtx_kuerzel($item); ?>} \hfill \textbf{<?php cvtx_antragsteller($item); ?>} \\ <?php cvtx_titel($item); ?> <?php if(cvtx_spd_has_ak_recommendation($item)): ?> \\ \textit{<?php cvtx_spd_ak_recommendation($item); ?>} <?php endif; ?>}
\textbf{<?php cvtx_kuerzel($item); ?>}\\
\textbf{<?php cvtx_antragsteller($item); ?>}\\
<?php cvtx_spd_tex_recipient($item); ?>
\par
\textbf{<?php cvtx_titel($item); ?>}\\
<?php cvtx_antragstext($item); ?>\\
<?php if (cvtx_has_begruendung($item)) { ?>
\newline
    \textbf{<?php cvtx_print_latex(__('Explanation', 'cvtx')); ?>}\\
    <?php cvtx_begruendung($item); ?>
<?php } ?>\par
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
<?php endif; ?>
<?php if (cvtx_spd_has_ak_recommendation($item)): ?>
\textbf{<?php cvtx_spd_ak_recommendation($item); ?>}\\
\newline
<?php endif; ?>
<?php if($col1) : ?>
\begin{linenumbers}
<?php endif; ?>
<?php cvtx_spd_version_ak($item); ?>
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
<?php $prev_antrag = true; ?>
\erow
<?php else: ?>
\end{linenumbers}
<?php endif; ?>

<?php
    }

    /* Show Amendment */
    else if ($item->post_type == 'cvtx_aeantrag') {
?>
  <?php if($running_antraege && !$col1): ?>
  \end{calstable}
  <?php $prev_antrag = false; $running_antraege = false; endif; ?>

% Add Bookmarks and Reference for Table of Contents
<?php   // Update agenda item if changed
        $this_antrag = get_post_meta($item->ID, 'cvtx_aeantrag_antrag', true);
        $this_top    = get_post_meta($this_antrag, 'cvtx_antrag_top', true);
        if ($top != $this_top) {
            $top  = $this_top;
?>
            \addcontentsline{toc}{section}{<?php cvtx_top($item); ?>}
            \section*{<?php cvtx_top($item); ?>}
<?php   }
        // Update resolution if changed
        if ($antrag != $this_antrag || !$running_aeantraege) {
            $antrag  = $this_antrag;
?>
            \addcontentsline{toc}{section}{\"Anderungsantr\"age zum <?php cvtx_kuerzel($antrag); ?>}
            \section*{\"Anderungsantr\"age zum <?php cvtx_antrag($item); ?>}
<?php   } ?>

<?php if(!$running_aeantraege && !$col1) : ?>
\begin{calstable} 
\colwidths{{1cm}{8cm}{8cm}}
\cals@paddingL=7pt
\cals@paddingR=7pt
\cals@paddingB=0pt
\cals@paddingT=7pt
\def\cals@cs@width{0pt}

\brow \erow
<?php $running_aeantraege = true; endif; ?>

<?php if($prev_aeantrag && !$col1): ?>
\hrule width \textwidth height 0.4pt depth 0pt\relax
<?php endif; ?>
<?php if(!$col1): ?>
\addtocounter{rows}{1}
\alignR\cell{
%\the\value{rows1}
\ifcounter{row\the\value{rows}}{\setcounter{loopTo}{\the\value{row\the\value{rows}}}\addtocounter{loopTo}{1}}{\setcounter{loopTo}{1}}
\setcounter{myLN}{1}
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

\alignL\cell{
\begin{myLN}
<?php else: ?>
\begin{linenumbers}
<?php endif; ?>
\addcontentsline{toc}{subsection}{\protect \hspace{2.3em} \textbf{<?php cvtx_kuerzel($item); ?>} \hfill \textbf{<?php cvtx_antragsteller($item); ?>} \\ <?php cvtx_spd_aeantrag_titel($item); ?> <?php if(cvtx_spd_has_ak_recommendation($item)): ?>\\ \textit{<?php cvtx_spd_ak_recommendation($item); ?>} <?php endif; ?>}
\textbf{<?php cvtx_kuerzel($item); ?>}\\
\textbf{<?php cvtx_antragsteller($item); ?>}\\
\textbf{<?php cvtx_spd_tex_recipient($item); ?>}\\
\newline
\textbf{<?php cvtx_spd_aeantrag_titel($item); ?>}\\
<?php cvtx_antragstext($item); ?>
<?php if (cvtx_has_begruendung($item)) { ?>
\newline\newline
    \textbf{<?php cvtx_print_latex(__('Explanation', 'cvtx')); ?>}\\
    <?php cvtx_begruendung($item); ?>
<?php } ?>
<?php if($col1): ?>
\end{linenumbers}
<?php else: ?>
\par
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
<?php endif; ?>
\textbf{<?php cvtx_spd_ak_recommendation($item); ?>}\\
\newline
<?php if($col1): ?>
\begin{linenumbers}
<?php endif; ?>
<?php cvtx_spd_version_ak($item); ?>
<?php if($col1): ?>
\end{linenumbers}
<?php else: ?>
\par
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
<?php $prev_aeantrag = true; ?>
\erow
<?php endif; ?>

<?php
    }
}
?>
  <?php if($running_aeantraege && !$col1): ?>
  \end{calstable}
  <?php $prev_aeantrag = false; $running_aeantraege = false; endif; ?>

  <?php if($running_antraege && !$col1): ?>
  \end{calstable}
  <?php $prev_antrag = false; $running_antraege = false; endif; ?>
<?php if(!$col1): ?>
\closeoutputstream{linenumbers}
<?php endif; ?>

\end{document}