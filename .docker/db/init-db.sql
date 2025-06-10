\connect postgres

SELECT 'CREATE DATABASE expelliarmus'
WHERE NOT EXISTS (SELECT
                  FROM pg_database
                  WHERE datname = 'expelliarmus')
\gexec

SELECT 'CREATE DATABASE expelliarmus_test'
WHERE NOT EXISTS (SELECT
                  FROM pg_database
                  WHERE datname = 'expelliarmus_test')
\gexec
