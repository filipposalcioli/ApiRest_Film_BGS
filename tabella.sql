CREATE TABLE oscar (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(255) NOT NULL,
    year        YEAR         NOT NULL,
    category   VARCHAR(255) NOT NULL, 
    winner   VARCHAR(255) NOT NULL
);

