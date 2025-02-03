CREATE TABLE Categories (
    CategoryID SERIAL PRIMARY KEY,             
    CategoryName VARCHAR(100) NOT NULL     
);

CREATE OR REPLACE FUNCTION check_category_limit()
RETURNS TRIGGER AS $$
BEGIN
    IF (SELECT COUNT(*) FROM Categories) >= 12 THEN
        RAISE EXCEPTION 'Нельзя добавить больше 12 категорий';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER limit_categories_trigger
BEFORE INSERT ON Categories
FOR EACH ROW
EXECUTE FUNCTION check_category_limit();

-- psql -U <username> -d <database_name> -f create_categories.sql