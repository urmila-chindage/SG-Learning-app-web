SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

DELIMITER $$
CREATE TRIGGER `delete_assessment_questions_connection` AFTER DELETE ON `assessments`
 FOR EACH ROW BEGIN
DELETE FROM assessment_questions WHERE aq_assesment_id = OLD.id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `overall_grade_on_assessment_after_add` AFTER INSERT ON `assessment_attempts`
 FOR EACH ROW BEGIN

DECLARE assessment_sum, assessment_count, assignment_sum, assignment_count, course_percentage INT(11);
DECLARE final_grade VARCHAR(3);

SELECT cs_percentage INTO course_percentage 
FROM course_subscription 
WHERE cs_user_id = NEW.aa_user_id AND cs_course_id = NEW.aa_course_id;


SELECT COUNT(id) INTO assessment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.aa_course_id;

SELECT COUNT(id) INTO assignment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.aa_course_id;

SELECT SUM(assessment_attempts.aa_grade_higher_value) INTO assessment_sum  
FROM assessment_attempts 
WHERE `aa_user_id` = NEW.aa_user_id AND `aa_course_id` = NEW.aa_course_id AND aa_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.aa_course_id) AND aa_latest = '1';

SELECT SUM(descrptive_test_user_answered.dtua_grade_higher_value) INTO assignment_sum  
FROM descrptive_test_user_answered 
WHERE `dtua_user_id` = NEW.aa_user_id AND `dtua_course_id` = NEW.aa_course_id AND dtua_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.aa_course_id);


SET assessment_sum = IF(assessment_sum IS NULL, 0, assessment_sum);
SET assignment_sum = IF(assignment_sum IS NULL, 0, assignment_sum);
SET course_percentage = IF(course_percentage IS NULL, 0, course_percentage);

SELECT gr_name INTO final_grade 
FROM grades 
WHERE  ROUND((assessment_sum+assignment_sum+course_percentage)/(assessment_count+assignment_count+1)) BETWEEN gr_range_from AND gr_range_to;

UPDATE course_subscription 
SET cs_auto_grade = final_grade 
WHERE cs_course_id = NEW.aa_course_id AND cs_user_id = NEW.aa_user_id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `overall_grade_on_assessment_after_update` AFTER UPDATE ON `assessment_attempts`
 FOR EACH ROW BEGIN

DECLARE assessment_sum, assessment_count, assignment_sum, assignment_count, course_percentage INT(11);
DECLARE final_grade VARCHAR(3);

IF(NEW.aa_grade <> OLD.aa_grade) THEN

SELECT cs_percentage INTO course_percentage 
FROM course_subscription 
WHERE cs_user_id = NEW.aa_user_id AND cs_course_id = NEW.aa_course_id;


SELECT COUNT(id) INTO assessment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.aa_course_id;

SELECT COUNT(id) INTO assignment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.aa_course_id;

SELECT SUM(assessment_attempts.aa_grade_higher_value) INTO assessment_sum  
FROM assessment_attempts 
WHERE `aa_user_id` = NEW.aa_user_id AND `aa_course_id` = NEW.aa_course_id AND aa_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.aa_course_id) AND aa_latest = '1';

SELECT SUM(descrptive_test_user_answered.dtua_grade_higher_value) INTO assignment_sum  
FROM descrptive_test_user_answered 
WHERE `dtua_user_id` = NEW.aa_user_id AND `dtua_course_id` = NEW.aa_course_id AND dtua_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.aa_course_id);

SET assessment_sum = IF(assessment_sum IS NULL, 0, assessment_sum);
SET assignment_sum = IF(assignment_sum IS NULL, 0, assignment_sum);
SET course_percentage = IF(course_percentage IS NULL, 0, course_percentage);

SELECT gr_name INTO final_grade 
FROM grades 
WHERE  ROUND((assessment_sum+assignment_sum+course_percentage)/(assessment_count+assignment_count+1)) BETWEEN gr_range_from AND gr_range_to;

UPDATE course_subscription 
SET cs_auto_grade = final_grade 
WHERE cs_course_id = NEW.aa_course_id AND cs_user_id = NEW.aa_user_id;

END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `overall_grade_on_assessment_before_add` BEFORE INSERT ON `assessment_attempts`
 FOR EACH ROW BEGIN

DECLARE grade_value_higher INT(11);

SELECT gr_range_to INTO grade_value_higher 
FROM grades 
WHERE gr_name = NEW.aa_grade; 
SET NEW.aa_grade_higher_value =  IF(grade_value_higher IS NULL, 0, grade_value_higher);

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `overall_grade_on_assessment_before_update` BEFORE UPDATE ON `assessment_attempts`
 FOR EACH ROW BEGIN

DECLARE grade_value_higher INT(11);

IF(NEW.aa_grade <> OLD.aa_grade) THEN

SELECT gr_range_to INTO grade_value_higher 
FROM grades 
WHERE gr_name = NEW.aa_grade; 
SET NEW.aa_grade_higher_value =  IF(grade_value_higher IS NULL, 0, grade_value_higher);

END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_bundle_rating_on_add` AFTER INSERT ON `bundle_ratings`
 FOR EACH ROW BEGIN 

DECLARE rating_sum, rating_count INT;

SELECT SUM(cc_rating), COUNT(cc_rating) INTO rating_sum, rating_count FROM `bundle_ratings` WHERE `cc_bundle_id` = NEW.cc_bundle_id AND cc_status = '1';

UPDATE item_sort_order SET iso_item_rating = (rating_sum/rating_count) WHERE iso_item_id = NEW.cc_bundle_id AND iso_item_type = 'bundle';

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_bundle_rating_on_update` AFTER UPDATE ON `bundle_ratings`
 FOR EACH ROW BEGIN 

DECLARE rating_sum, rating_count INT;

SELECT SUM(cc_rating), COUNT(cc_rating) INTO rating_sum, rating_count FROM `bundle_ratings` WHERE `cc_bundle_id` = NEW.cc_bundle_id AND cc_status = '1';

UPDATE item_sort_order SET iso_item_rating = (rating_sum/rating_count) WHERE iso_item_id = NEW.cc_bundle_id AND iso_item_type = 'bundle';

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `register_bundle_sort_order` AFTER INSERT ON `catalogs`
 FOR EACH ROW BEGIN INSERT INTO item_sort_order (iso_item_type, iso_item_id, iso_item_name, iso_item_sort_order, iso_item_price, iso_item_discount_price, iso_item_status, iso_item_deleted,iso_item_is_free,iso_account_id) VALUES ('bundle', NEW.id, NEW.c_title, '0', NEW.c_price, NEW.c_discount, '0', '0',NEW.c_is_free,NEW.c_account_id); END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `remove_bundle_data_from_other_tables` BEFORE DELETE ON `catalogs`
 FOR EACH ROW BEGIN DELETE FROM item_sort_order WHERE iso_item_id = OLD.id AND iso_item_type = 'bundle'; END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_bundle_sort_oder` AFTER UPDATE ON `catalogs`
 FOR EACH ROW BEGIN 


IF(NEW.c_status = '0' OR NEW.c_deleted = '1')
THEN

UPDATE item_sort_order SET iso_item_name = NEW.c_title, iso_item_price = NEW.c_price, iso_item_discount_price = NEW.c_discount, iso_item_status = NEW.c_status, iso_item_deleted = NEW.c_deleted,iso_item_is_free = NEW.c_is_free, iso_account_id = NEW.c_account_id,iso_item_featured = 0,iso_item_popular = 0 WHERE iso_item_type="bundle" AND iso_item_id = NEW.id; 

ELSE

UPDATE item_sort_order SET iso_item_name = NEW.c_title, iso_item_price = NEW.c_price, iso_item_discount_price = NEW.c_discount, iso_item_status = NEW.c_status, iso_item_deleted = NEW.c_deleted,iso_item_is_free = NEW.c_is_free, iso_account_id = NEW.c_account_id WHERE iso_item_type="bundle" AND iso_item_id = NEW.id;

END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `register_course_sort_order` AFTER INSERT ON `course_basics`
 FOR EACH ROW BEGIN INSERT INTO item_sort_order (iso_item_type, iso_item_id, iso_item_name, iso_item_sort_order, iso_item_price, iso_item_discount_price, iso_item_status, iso_item_deleted, iso_account_id,iso_item_is_free) VALUES ('course', NEW.id, NEW.cb_title, '0', NEW.cb_price, NEW.cb_discount, '0', '0', NEW.cb_account_id,NEW.cb_is_free); END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `remove_course_data_from_other_tables` BEFORE DELETE ON `course_basics`
 FOR EACH ROW BEGIN
/*DELETE FROM announcement WHERE an_course_id = OLD.id;
DELETE FROM course_lectures WHERE cl_course_id = OLD.id;
DELETE FROM course_subscription WHERE cs_course_id = OLD.id; 
DELETE FROM course_perfomance WHERE cp_course_id = OLD.id;
DELETE FROM course_tutors WHERE ct_course_id = OLD.id; 
DELETE FROM section WHERE s_course_id = OLD.id; 
DELETE FROM item_sort_order WHERE iso_item_id = OLD.id AND iso_item_type = 'course';*/
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_course_sort_oder` AFTER UPDATE ON `course_basics`
 FOR EACH ROW BEGIN
IF(NEW.cb_status = '0' OR NEW.cb_deleted = '1')
THEN

UPDATE item_sort_order SET iso_item_name = NEW.cb_title, iso_item_price = NEW.cb_price, iso_item_discount_price = NEW.cb_discount, iso_item_status = NEW.cb_status, iso_item_deleted = NEW.cb_deleted, iso_account_id = NEW.cb_account_id,iso_item_is_free = NEW.cb_is_free, iso_item_popular = 0, iso_item_featured = 0 WHERE iso_item_type="course" AND iso_item_id = NEW.id; 

ELSE

UPDATE item_sort_order SET iso_item_name = NEW.cb_title, iso_item_price = NEW.cb_price, iso_item_discount_price = NEW.cb_discount, iso_item_status = NEW.cb_status, iso_item_deleted = NEW.cb_deleted, iso_account_id = NEW.cb_account_id,iso_item_is_free = NEW.cb_is_free WHERE iso_item_type="course" AND iso_item_id = NEW.id; 

END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_course_subscribed_count` AFTER UPDATE ON `course_consolidated_report`
 FOR EACH ROW BEGIN  

DECLARE enrolled_count,percentage_count INT(11);

SELECT SUM(ccr_total_enrolled) INTO enrolled_count
FROM course_consolidated_report
WHERE ccr_course_id = NEW.ccr_course_id;

SELECT SUM(ccr_total_completed) INTO percentage_count
FROM course_consolidated_report
WHERE ccr_course_id = NEW.ccr_course_id;

UPDATE course_basics SET cb_total_enrolled_users = enrolled_count WHERE id = NEW.ccr_course_id;

UPDATE course_basics SET cb_course_completed_count = percentage_count WHERE id = NEW.ccr_course_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `remove_lecture_data_from_other_tables` BEFORE DELETE ON `course_lectures`
 FOR EACH ROW BEGIN
DELETE FROM lecture_override WHERE lo_lecture_id = OLD.id;
DELETE FROM assessments WHERE a_lecture_id = OLD.id;
DELETE FROM assessment_attempts WHERE aa_lecture_id = OLD.id;
DELETE FROM assessment_report WHERE ar_lecture_id = OLD.id;
DELETE FROM assessment_rules WHERE lecture_id = OLD.id;
DELETE FROM live_lectures WHERE ll_lecture_id = OLD.id;
DELETE FROM survey WHERE s_lecture_id = OLD.id;
DELETE FROM survey_questions WHERE sq_lecture_id = OLD.id;
DELETE FROM survey_user_response WHERE sur_lecture_id	 = OLD.id;
DELETE FROM descrptive_tests WHERE dt_lecture_id = OLD.id;
DELETE FROM descrptive_test_user_answered WHERE dtua_lecture_id = OLD.id;
DELETE FROM descrptive_test_answers WHERE da_lecture_id = OLD.id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_course_lecture_count` AFTER UPDATE ON `course_lectures`
 FOR EACH ROW BEGIN  

DECLARE total_lectures, active_section_lectures, old_section_lectures, new_section_lectures INT(11);



IF OLD.cl_status <> NEW.cl_status OR OLD.cl_deleted <> NEW.cl_deleted THEN 



    SELECT COUNT(id) INTO total_lectures 

    FROM course_lectures WHERE course_lectures.cl_lecture_type = NEW.cl_lecture_type AND course_lectures.cl_course_id = NEW.cl_course_id AND cl_status='1' AND cl_deleted='0';



    IF NEW.cl_lecture_type = '1' THEN

        UPDATE course_basics SET course_basics.cb_video_count = total_lectures WHERE id = NEW.cl_course_id; 

    ELSEIF NEW.cl_lecture_type = '2' THEN

        UPDATE course_basics SET course_basics.cb_docs_count = total_lectures WHERE id = NEW.cl_course_id;

    ELSEIF NEW.cl_lecture_type = '3' THEN

        UPDATE course_basics SET course_basics.cb_assessment_count = total_lectures WHERE id = NEW.cl_course_id;
        
    ELSEIF NEW.cl_lecture_type = '4' THEN

        UPDATE course_basics SET course_basics.cb_video_count = total_lectures WHERE id = NEW.cl_course_id;
        
    ELSEIF NEW.cl_lecture_type = '5' THEN

        UPDATE course_basics SET course_basics.cb_docs_count = total_lectures WHERE id = NEW.cl_course_id;

    ELSEIF NEW.cl_lecture_type = '7' THEN

        UPDATE course_basics SET course_basics.cb_live_count = total_lectures WHERE id = NEW.cl_course_id;

    END IF;



    SELECT COUNT(id) INTO active_section_lectures 

    FROM course_lectures 

    WHERE cl_deleted = '0' AND cl_status = '1' AND cl_section_id = NEW.cl_section_id  AND cl_course_id = NEW.cl_course_id;



    IF active_section_lectures = 0 THEN

        UPDATE section SET s_status = '0' WHERE section.id = NEW.cl_section_id;

    ELSE

        UPDATE section SET s_status = '1' WHERE section.id = NEW.cl_section_id;

    END IF;



END IF;



IF OLD.cl_order_no <> NEW.cl_order_no THEN 



    SELECT COUNT(id) INTO active_section_lectures 

    FROM course_lectures 

    WHERE cl_deleted = '0' AND cl_status = '1' AND cl_section_id = NEW.cl_section_id  AND cl_course_id = NEW.cl_course_id;



    IF active_section_lectures = 0 

    THEN 

        UPDATE section SET s_status = '0' WHERE section.id = NEW.cl_section_id;

    ELSE 

        UPDATE section SET s_status = '1' WHERE section.id = NEW.cl_section_id;

    END IF;



END IF;



IF OLD.cl_section_id <> NEW.cl_section_id THEN 

    

    SELECT COUNT(id) INTO old_section_lectures 

    FROM course_lectures 

    WHERE cl_deleted = '0' AND cl_status = '1' AND cl_section_id = OLD.cl_section_id  AND cl_course_id = OLD.cl_course_id;



    IF old_section_lectures = 0 THEN 

        UPDATE section SET s_status = '0' WHERE section.id = OLD.cl_section_id;

    ELSE

        UPDATE section SET s_status = '1' WHERE section.id = OLD.cl_section_id;

    END IF;



    SELECT COUNT(id) INTO new_section_lectures 

    FROM course_lectures 

    WHERE cl_deleted = '0' AND cl_status = '1' AND cl_section_id = NEW.cl_section_id  AND cl_course_id = NEW.cl_course_id;



    IF new_section_lectures = 0 THEN 

        UPDATE section SET s_status = '0' WHERE section.id = NEW.cl_section_id;

    ELSE 

        UPDATE section SET s_status = '1' WHERE section.id = NEW.cl_section_id;

    END IF;



END IF;



END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_course_lecture_count_on_delete` AFTER DELETE ON `course_lectures`
 FOR EACH ROW BEGIN  
DECLARE total_lectures, active_section_lectures INT(11);

    SELECT COUNT(id) INTO total_lectures 
    FROM course_lectures WHERE course_lectures.cl_lecture_type = OLD.cl_lecture_type AND course_lectures.cl_course_id = OLD.cl_course_id AND cl_status='1' AND cl_deleted='0';

    IF OLD.cl_lecture_type = '1' THEN
        UPDATE course_basics SET course_basics.cb_video_count = total_lectures WHERE id = OLD.cl_course_id; 
    ELSEIF OLD.cl_lecture_type = '2' THEN
        UPDATE course_basics SET course_basics.cb_docs_count = total_lectures WHERE id = OLD.cl_course_id;
    ELSEIF OLD.cl_lecture_type = '3' THEN
        UPDATE course_basics SET course_basics.cb_assessment_count = total_lectures WHERE id = OLD.cl_course_id;
    ELSEIF OLD.cl_lecture_type = '7' THEN
        UPDATE course_basics SET course_basics.cb_live_count = total_lectures WHERE id = OLD.cl_course_id;
    END IF;

    SELECT COUNT(id) INTO active_section_lectures 
    FROM course_lectures 
    WHERE cl_deleted = '0' AND cl_status = '1' AND cl_section_id = OLD.cl_section_id  AND cl_course_id = OLD.cl_course_id;

    IF active_section_lectures = 0 THEN
        UPDATE section SET s_status = '0' WHERE section.id = OLD.cl_section_id;
    ELSE
        UPDATE section SET s_status = '1' WHERE section.id = OLD.cl_section_id;
    END IF;
    
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `add_course_perfomance_sum` AFTER INSERT ON `course_perfomance`
 FOR EACH ROW BEGIN  

DECLARE course_likes,course_dislikes, forum_likes,forum_dislikes INT;

SELECT SUM(cp_course_likes), SUM(cp_course_dislikes), SUM(cp_forum_likes), SUM(cp_forum_dislikes) INTO course_likes,course_dislikes, forum_likes,forum_dislikes 
FROM course_perfomance WHERE cp_course_id = NEW.cp_course_id;


UPDATE course_basics SET cb_course_likes = course_likes, cb_course_dislikes = course_dislikes, cb_course_forum_likes = forum_likes, cb_course_forum_dislikes = forum_dislikes WHERE id = NEW.cp_course_id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_course_perfomance_sum` AFTER UPDATE ON `course_perfomance`
 FOR EACH ROW BEGIN  

DECLARE course_likes,course_dislikes, forum_likes,forum_dislikes INT;

SELECT SUM(cp_course_likes), SUM(cp_course_dislikes), SUM(cp_forum_likes), SUM(cp_forum_dislikes) INTO course_likes,course_dislikes, forum_likes,forum_dislikes 
FROM course_perfomance WHERE cp_course_id = NEW.cp_course_id;


UPDATE course_basics SET cb_course_likes = course_likes, cb_course_dislikes = course_dislikes, cb_course_forum_likes = forum_likes, cb_course_forum_dislikes = forum_dislikes WHERE id = NEW.cp_course_id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_course_perfomance` AFTER INSERT ON `course_ratings`
 FOR EACH ROW BEGIN 

SET 
  @totalrows = ( SELECT COUNT(*) FROM course_perfomance WHERE cp_course_id = NEW.cc_course_id AND cp_institute_id = NEW.cc_institute_id  ),

   @course_like = IF(NEW.cc_rating>=3,1,0),
   @course_dislike = IF(NEW.cc_rating<3,1,0);
 
IF( @totalrows > 0) THEN

UPDATE course_perfomance SET cp_course_likes = cp_course_likes+@course_like, cp_course_dislikes = cp_course_dislikes+@course_dislike WHERE cp_course_id = NEW.cc_course_id AND cp_institute_id = NEW.cc_institute_id;

ELSE

INSERT INTO course_perfomance (cp_course_id, cp_institute_id, cp_course_institute_map, cp_course_likes, cp_course_dislikes, cp_forum_likes, cp_forum_dislikes) VALUES (NEW.cc_course_id, NEW.cc_institute_id, CONCAT(NEW.cc_course_id,'_', NEW.cc_institute_id), @course_like, @course_dislike, '0', '0');

END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `consolidated_count_update` AFTER UPDATE ON `course_subscription`
 FOR EACH ROW BEGIN

DECLARE completed_count INT(11);

IF( OLD.cs_completion_registered <> NEW.cs_completion_registered )  THEN

SELECT COUNT(*) INTO completed_count  
FROM course_subscription 
WHERE cs_course_id = NEW.cs_course_id AND cs_user_institute	= NEW.cs_user_institute AND cs_completion_registered = '1';

UPDATE course_consolidated_report SET ccr_total_completed = completed_count WHERE ccr_course_id = NEW.cs_course_id AND ccr_institute_id = NEW.cs_user_institute;

END IF;


END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `overall_grade_on_percentage_update` BEFORE UPDATE ON `course_subscription`
 FOR EACH ROW BEGIN

DECLARE assessment_sum, assessment_count, assignment_sum, assignment_count, completed_count INT(11);
DECLARE final_grade VARCHAR(3);

IF( (NEW.cs_percentage - OLD.cs_old_percentage) >= 5 ) THEN

SELECT COUNT(id) INTO assessment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.cs_course_id;

SELECT COUNT(id) INTO assignment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.cs_course_id;

SELECT SUM(assessment_attempts.aa_grade_higher_value) INTO assessment_sum  
FROM assessment_attempts 
WHERE `aa_user_id` = NEW.cs_user_id AND `aa_course_id` = NEW.cs_course_id AND aa_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.cs_course_id) AND aa_latest = '1';

SELECT SUM(descrptive_test_user_answered.dtua_grade_higher_value) INTO assignment_sum  
FROM descrptive_test_user_answered 
WHERE `dtua_user_id` = NEW.cs_user_id AND `dtua_course_id` = NEW.cs_course_id AND dtua_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.cs_course_id);


SET assessment_sum = IF(assessment_sum IS NULL, 0, assessment_sum);
SET assignment_sum = IF(assignment_sum IS NULL, 0, assignment_sum);
SET NEW.cs_percentage = IF(NEW.cs_percentage IS NULL, 0, NEW.cs_percentage);


SELECT gr_name INTO final_grade 
FROM grades 
WHERE  ROUND((assessment_sum+assignment_sum+NEW.cs_percentage)/(assessment_count+assignment_count+1)) BETWEEN gr_range_from AND gr_range_to;

SET NEW.cs_auto_grade = final_grade;
SET NEW.cs_old_percentage = NEW.cs_percentage;

END IF;

IF( NEW.cs_percentage > 99 )  THEN
SET NEW.cs_completion_registered = '1';
END IF;


END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `register_user_name_for_subscription` BEFORE INSERT ON `course_subscription`
 FOR EACH ROW BEGIN  

DECLARE user_name VARCHAR(255);
DECLARE user_institute INT(11);

SELECT users.us_name, users.us_institute_id INTO user_name, user_institute  
FROM users WHERE users.id = NEW.cs_user_id;

SET NEW.cs_user_name = user_name, NEW.cs_user_institute = user_institute;


END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_consolidated_report_of_enrolled` AFTER INSERT ON `course_subscription`
 FOR EACH ROW BEGIN  

DECLARE enrolled_count INT(11);

SELECT COUNT(*) INTO enrolled_count 
FROM course_subscription 
WHERE cs_course_id = NEW.cs_course_id AND cs_user_institute	= NEW.cs_user_institute;

UPDATE course_consolidated_report SET ccr_total_enrolled = enrolled_count WHERE ccr_course_id = NEW.cs_course_id AND ccr_institute_id = NEW.cs_user_institute;

UPDATE course_basics SET cb_total_enrolled_users = enrolled_count WHERE id = NEW.cs_course_id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_consolidated_report_on_unsubscribe` AFTER DELETE ON `course_subscription`
 FOR EACH ROW BEGIN  

DECLARE enrolled_count,completed_count INT(11);

SELECT COUNT(*) INTO enrolled_count 
FROM course_subscription 
WHERE cs_course_id = OLD.cs_course_id AND cs_user_institute	= OLD.cs_user_institute;

UPDATE course_consolidated_report SET ccr_total_enrolled = enrolled_count WHERE ccr_course_id = OLD.cs_course_id AND ccr_institute_id = OLD.cs_user_institute;

SELECT COUNT(*) INTO completed_count  
FROM course_subscription 
WHERE cs_course_id = OLD.cs_course_id AND cs_user_institute	= OLD.cs_user_institute AND (cs_percentage > 99 OR cs_completion_registered = '1');

UPDATE course_consolidated_report SET ccr_total_completed = completed_count WHERE ccr_course_id = OLD.cs_course_id AND ccr_institute_id = OLD.cs_user_institute;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `overall_grade_on_assignment_after_add` AFTER INSERT ON `descrptive_test_user_answered`
 FOR EACH ROW BEGIN

DECLARE assessment_sum, assessment_count, assignment_sum, assignment_count, course_percentage INT(11);
DECLARE final_grade VARCHAR(3);

SELECT cs_percentage INTO course_percentage 
FROM course_subscription 
WHERE cs_user_id = NEW.dtua_user_id AND cs_course_id = NEW.dtua_course_id;


SELECT COUNT(id) INTO assessment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.dtua_course_id;

SELECT COUNT(id) INTO assignment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.dtua_course_id;

SELECT SUM(assessment_attempts.aa_grade_higher_value) INTO assessment_sum  
FROM assessment_attempts 
WHERE `aa_user_id` = NEW.dtua_user_id AND `aa_course_id` = NEW.dtua_course_id AND aa_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.dtua_course_id) AND aa_latest = '1';

SELECT SUM(descrptive_test_user_answered.dtua_grade_higher_value) INTO assignment_sum  
FROM descrptive_test_user_answered 
WHERE `dtua_user_id` = NEW.dtua_user_id AND `dtua_course_id` = NEW.dtua_course_id AND dtua_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.dtua_course_id);

SET assessment_sum = IF(assessment_sum IS NULL, 0, assessment_sum);
SET assignment_sum = IF(assignment_sum IS NULL, 0, assignment_sum);
SET course_percentage = IF(course_percentage IS NULL, 0, course_percentage);

SELECT gr_name INTO final_grade 
FROM grades 
WHERE  ROUND((assessment_sum+assignment_sum+course_percentage)/(assessment_count+assignment_count+1)) BETWEEN gr_range_from AND gr_range_to;

UPDATE course_subscription 
SET cs_auto_grade = final_grade 
WHERE cs_course_id = NEW.dtua_course_id AND cs_user_id = NEW.dtua_user_id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `overall_grade_on_assignment_after_update` AFTER UPDATE ON `descrptive_test_user_answered`
 FOR EACH ROW BEGIN

DECLARE assessment_sum, assessment_count, assignment_sum, assignment_count, course_percentage INT(11);
DECLARE final_grade VARCHAR(3);

IF(NEW.dtua_grade <> OLD.dtua_grade) THEN

SELECT cs_percentage INTO course_percentage 
FROM course_subscription 
WHERE cs_user_id = NEW.dtua_user_id AND cs_course_id = NEW.dtua_course_id;

SELECT COUNT(id) INTO assessment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.dtua_course_id;

SELECT COUNT(id) INTO assignment_count 
FROM `course_lectures` 
WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.dtua_course_id;


SELECT SUM(assessment_attempts.aa_grade_higher_value) INTO assessment_sum  
FROM assessment_attempts 
WHERE `aa_user_id` = NEW.dtua_user_id AND `aa_course_id` = NEW.dtua_course_id AND aa_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 3 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.dtua_course_id) AND aa_latest = '1';

SELECT SUM(descrptive_test_user_answered.dtua_grade_higher_value) INTO assignment_sum  
FROM descrptive_test_user_answered 
WHERE `dtua_user_id` = NEW.dtua_user_id AND `dtua_course_id` = NEW.dtua_course_id AND dtua_lecture_id IN (SELECT id FROM course_lectures WHERE `cl_lecture_type` = 8 AND cl_deleted = '0' AND cl_status = '1' AND cl_course_id = NEW.dtua_course_id);


SET assessment_sum = IF(assessment_sum IS NULL, 0, assessment_sum);
SET assignment_sum = IF(assignment_sum IS NULL, 0, assignment_sum);
SET course_percentage = IF(course_percentage IS NULL, 0, course_percentage);

SELECT gr_name INTO final_grade 
FROM grades 
WHERE  ROUND((assessment_sum+assignment_sum+course_percentage)/(assessment_count+assignment_count+1)) BETWEEN gr_range_from AND gr_range_to;

UPDATE course_subscription 
SET cs_auto_grade = final_grade 
WHERE cs_course_id = NEW.dtua_course_id AND cs_user_id = NEW.dtua_user_id;

END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `overall_grade_on_assignment_before_add` BEFORE INSERT ON `descrptive_test_user_answered`
 FOR EACH ROW BEGIN

DECLARE grade_value_higher INT(11);


SELECT gr_range_to INTO grade_value_higher 
FROM grades 
WHERE gr_name = NEW.dtua_grade; 
SET NEW.dtua_grade_higher_value =  IF(grade_value_higher IS NULL, 0, grade_value_higher);


END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `overall_grade_on_assignment_before_update` BEFORE UPDATE ON `descrptive_test_user_answered`
 FOR EACH ROW BEGIN

DECLARE grade_value_higher INT(11);

IF(NEW.dtua_grade <> OLD.dtua_grade) OR (OLD.dtua_grade IS NULL) THEN

SELECT gr_range_to INTO grade_value_higher 
FROM grades 
WHERE gr_name = NEW.dtua_grade; 
SET NEW.dtua_grade_higher_value =  IF(grade_value_higher IS NULL, 0, grade_value_higher);

END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_point` AFTER INSERT ON `log_activity`
 FOR EACH ROW BEGIN 

IF(NEW.la_user_id != 0) THEN

UPDATE log_activity_points SET lap_user_points = lap_user_points + NEW.la_points WHERE lap_user_id = NEW.la_user_id;

END IF;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `delete_field_on_block_removal` AFTER DELETE ON `profile_blocks`
 FOR EACH ROW BEGIN

DELETE FROM profile_fields WHERE pf_block_id = OLD.id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `register_tags_label_on_insert` BEFORE INSERT ON `questions`
 FOR EACH ROW BEGIN


SET @tags_label = (SELECT GROUP_CONCAT(tg_name) FROM tags WHERE FIND_IN_SET (tags.id, (NEW.q_tags)));

SET NEW.q_tags_label = @tags_label;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `register_tags_label_on_update` BEFORE UPDATE ON `questions`
 FOR EACH ROW BEGIN


SET @tags_label = (SELECT GROUP_CONCAT(tg_name) FROM tags WHERE FIND_IN_SET (tags.id, (NEW.q_tags)));

SET NEW.q_tags_label = @tags_label;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `survey_response_received` AFTER INSERT ON `survey_user_response`
 FOR EACH ROW BEGIN

UPDATE survey SET s_response_received = '1' WHERE survey.id = NEW.sur_survey_id;

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `log_point_creator` AFTER INSERT ON `users`
 FOR EACH ROW BEGIN 

IF(NEW.us_role_id = 2) THEN

INSERT INTO `log_activity_points` (`lap_user_id`, `lap_user_name`, `lap_user_points`) VALUES (NEW.id, NEW.us_name, '0');

END IF;

INSERT INTO `user_messages` (`um_user_id`,`um_messages`) VALUES (NEW.id,'{}');

END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `remove_user_data_from_other_tables` BEFORE DELETE ON `users`
 FOR EACH ROW BEGIN DELETE FROM assessment_attempts WHERE aa_user_id = OLD.id; DELETE FROM assessment_report WHERE ar_user_id = OLD.id; DELETE FROM course_subscription WHERE cs_user_id = OLD.id; DELETE FROM course_tutors WHERE ct_tutor_id = OLD.id; DELETE FROM descrptive_test_answers WHERE da_user_id = OLD.id; DELETE FROM descrptive_test_user_answered WHERE dtua_user_id = OLD.id; DELETE FROM event_participants WHERE ep_user_id = OLD.id; DELETE FROM log_activity WHERE la_user_id = OLD.id; DELETE FROM log_activity_points WHERE lap_user_id = OLD.id; DELETE FROM survey_user_response WHERE sur_user_id = OLD.id OR sur_tutor_id = OLD.id; DELETE FROM bundle_subscription WHERE bs_user_id = OLD.id; DELETE FROM payment_history WHERE ph_user_id = OLD.id; DELETE FROM course_wishlist WHERE cw_user_id = OLD.id; DELETE FROM purchase_history WHERE ph_user_id = OLD.id; END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_user_name_course_subscription` AFTER UPDATE ON `users`
 FOR EACH ROW BEGIN 

IF(NEW.us_name <> OLD.us_name OR NEW.us_groups <> OLD.us_groups) THEN

UPDATE course_subscription SET course_subscription.cs_user_name = NEW.us_name, course_subscription.cs_user_groups = NEW.us_groups WHERE course_subscription.cs_user_id = NEW.id;

END IF;

END
$$
DELIMITER ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;