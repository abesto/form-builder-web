BUILDER_SOURCES=actions.js builder.js remote.js utils.js props.js translation.js

all: builder.min.js

clean:
	rm -f *.min.js

builder_big.js: $(BUILDER_SOURCES)
	cat $(BUILDER_SOURCES) > $@

builder.min.js: builder_big.js
	cat ../PREAMBLE > $@
	yuicompressor $< >> $@
