#!/bin/sh

cat doc.tex | grep -v '^// TODO' > doc_clean.tex
pdflatex  -interaction=nonstopmode "\input" doc_clean.tex
mv doc_clean.pdf doc.pdf

killall evince
evince ./doc.pdf &
