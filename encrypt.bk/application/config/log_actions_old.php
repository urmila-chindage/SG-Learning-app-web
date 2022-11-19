<?php 
$log_actions = array();
$log_actions['student_registered'] = array(
                                            'message' => '{student_name} registed {course_name} to SGlearningapp',
                                            'points' => 10
                                     );

$config['log_action'] = $log_actions;
?>