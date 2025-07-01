
-- Example of SQL request

-- To see all the carpools from the driver (13 & 14) with validate reviews
SELECT 
    c.*, 
    rev.*
FROM 
    carpools c
JOIN 
    reviews rev ON c.driver_id = rev.driver_id AND rev.validate = true
WHERE 
    c.driver_id IN (13, 14)
ORDER BY 
    c.day ASC;