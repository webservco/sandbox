/**
Stuff
Simple and versatile structure.
*/
CREATE TABLE stuff_item (

    item_id INT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    parent_item_id INT(8) UNSIGNED DEFAULT NULL,

    item_name VARCHAR(90) NOT NULL,
    item_description VARCHAR(255) NOT NULL DEFAULT '',
    -- item_quantity SMALLINT(5) UNSIGNED NOT NULL DEFAULT 1,

    PRIMARY KEY (item_id),
    KEY k_item_name (item_name),

    CONSTRAINT fk_item_parent FOREIGN KEY (parent_item_id)
    REFERENCES stuff_item (item_id)
    ON UPDATE CASCADE ON DELETE CASCADE

) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
