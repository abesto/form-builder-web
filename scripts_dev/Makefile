SOURCES=forms_table.js login.js builder/htmlize.js
OBJECTS=$(SOURCES:.js=.min.js)

all: $(OBJECTS) builder_app

%.min.js: %.js
	cat PREAMBLE > $@
	yuicompressor $< >> $@

builder_app:
	cd builder && $(MAKE)

clean:
	rm -f *.min.js 
	cd builder && $(MAKE) clean

install: all
	cp *.min.js ../scripts/
	cp builder/*.min.js ../scripts/

