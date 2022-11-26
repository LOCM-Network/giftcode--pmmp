-- #! mysql
-- #{ table
-- #    { init
CREATE TABLE IF NOT EXISTS giftcode (
      uuid TEXT,
      code TEXT
);
-- #    }
-- #    { insert
-- #      :uuid string
-- #      :code string
INSERT INTO giftcode (uuid, code) VALUES (:uuid, :code);
-- #    }
-- #    { select
-- #      :uuid string
SELECT * FROM giftcode WHERE uuid = :uuid;
-- #    }
-- #    { update
-- #      :uuid string
-- #      :code string
DELETE FROM giftcode WHERE uuid = :uuid AND code = :code;
-- #    }
-- # }