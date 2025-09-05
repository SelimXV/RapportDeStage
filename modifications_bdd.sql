DELIMITER //
CREATE TRIGGER assign_teacher_on_stage_creation
BEFORE INSERT ON stage
FOR EACH ROW
BEGIN
    IF NEW.id_prof IS NULL THEN
        SET NEW.id_prof = (
            SELECT u.id 
            FROM user u 
            WHERE u.id_statut = 1 
            ORDER BY (
                SELECT COUNT(*) 
                FROM stage s 
                WHERE s.id_prof = u.id
            ) ASC 
            LIMIT 1
        );
    END IF;
END//
DELIMITER ;

DELIMITER //
CREATE TRIGGER validate_teacher_assignment
BEFORE UPDATE ON stage
FOR EACH ROW
BEGIN
    IF NEW.id_prof IS NOT NULL THEN
        IF NOT EXISTS (
            SELECT 1 FROM user 
            WHERE id = NEW.id_prof AND id_statut = 1
        ) THEN
            SIGNAL SQLSTATE '45000' 
            SET MESSAGE_TEXT = 'L\'enseignant assigné doit avoir le statut professeur';
        END IF;
    END IF;
END//
DELIMITER ;