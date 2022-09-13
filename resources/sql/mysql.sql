-- #! mysql

-- # { economy
-- # { teleporter
-- # { teleporters
-- # { init
CREATE TABLE IF NOT EXISTS teleporters
(
    id         INTEGER PRIMARY KEY AUTO_INCREMENT,
    name       TEXT    NOT NULL,
    world_name TEXT    NOT NULL,
    x          INTEGER NOT NULL,
    y          INTEGER NOT NULL,
    z          INTEGER NOT NULL
);
-- # }

-- # { create
-- #    :world_name string
-- #    :name string
-- #    :x int
-- #    :y int
-- #    :z int
INSERT INTO teleporters (world_name, name, x, y, z)
VALUES (:world_name, :name, :x, :y, :z);
-- # }

-- # { seq
SHOW TABLE STATUS WHERE name = "teleporters";
-- # }

-- # { load
SELECT *
FROM teleporters;
-- # }

-- # { delete
-- #    :id int
DELETE
FROM teleporters
WHERE id = :id;
-- # }

-- # { drop
DROP TABLE IF EXISTS teleporters;
-- # }
-- # }
-- # }
-- # }
