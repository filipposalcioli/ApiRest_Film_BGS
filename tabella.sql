CREATE TABLE oscar (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    titolo      VARCHAR(255) NOT NULL,
    anno        YEAR         NOT NULL,
    categoria   VARCHAR(255) NOT NULL,   -- es. "Best Picture"
    vincitore   VARCHAR(255)            -- regista, attore, ecc.
);