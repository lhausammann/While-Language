# Install
<code>
docker-compose up -d
docker exec -it parser-app bash
composer install
</code>

# Execute language (script defined in the command itself)
bin/console app:language 

# Parse & evaluate mathematical expressions
- bin/console app:calculator "7+7*2"
- bin/console app:calculator "a*3=3" --context="a=1"
- bin/console app:calculator "true AND false"

# Expressions in mathematical parser
```while
AND, OR
=, ~, <, >
+, -, *, /, ()
```

# Statements in while-language (toy language, but turing complete)
```while
WHILE <expr>;
END; (after while)
SET <Identifier> = <expr>;
PRINT <expr>;
```
Use ```SET = <``` for input.

** Example **
```while
SET running=true;
PRINT "Enter your name or 'exit' to quit";
WHILE (running);
    SET EXIT = "exit";
    SET innerLoop = true;
    PRINT "Dein Name:";
    SET name = <;
    WHILE (name ~ EXIT AND innerLoop=true);
        SET innerLoop = false;
        PRINT "Dein Name ist #{name}";
    END;
    WHILE (name = EXIT AND innerLoop=true);
        SET innerLoop = false;
        SET running = false;
        PRINT "Bye!";
    END;
END;
```
```bin/console app:language <scriptName>```
