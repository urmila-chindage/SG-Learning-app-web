<?php
$notify_actions = array();

//Registered to site.
$notify_actions['student_registered'] = array(
    'specific' => array(
        'message' => 'You have been registed to SGlearningapp.',
        'link' => 'login'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Student named {student_name} has been registered.',
            'link' => 'admin/user/?&filter=active&offset=1'
        ),
        'multiple' => array(
            'message' => '{number} new registration has been done.',
            'link' => 'admin/user/?&filter=active&offset=1'
        )
    ),
    'opposite_action' => array(),
);
//Account activated by Admin/Authorized user. {Normal,Bulk}
$notify_actions['student_account_activated'] = array(
    'specific' => array(
        'message' => 'Your account has been activated.',
        'link' => 'login'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Account of student named {student_name} has been activated.',
            'link' => 'admin/user/?&filter=not-approved&offset=1'
        ),
        'multiple' => array(
            'message' => '{number} activations has done in SGlearningapp.',
            'link' => 'admin/user/?&filter=active&offset=1'
        )
    ),
    'invertible' => true,
    'opposite_action' => array('student_account_deactivated'),
);
//Account deactivated by Admin/Authorized user. {Normal,Bulk}
$notify_actions['student_account_deactivated'] = array(
    'specific' => array(
        'message' => 'Your account has been deactivated.',
        'link' => 'login'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Account of student named {student_name} has been deactivated.',
            'link' => 'admin/user/?&filter=inactive&offset=1'
        ),
        'multiple' => array(
            'message' => '{number} deactivations has done in SGlearningapp.',
            'link' => 'admin/user/?&filter=inactive&offset=1'
        )
    ),
    'invertible' => true,
    'opposite_action' => array('student_account_activated'),
);
//Account approved by Admin/Authorized user. {Normal,Bulk}
$notify_actions['student_account_approved'] = array(
    'specific' => array(
        'message' => 'You account has been approved.',
        'link' => 'login'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Account of student named {student_name} has been approved.',
            'link' => 'admin/user/?&filter=inactive&offset=1'
        ),
        'multiple' => array(
            'message' => '{number} accounts has been approved in SGlearningapp.',
            'link' => 'admin/user/?&filter=active&offset=1'
        )
    ),
    'opposite_action' => array(),
);
//Account created bulk by Admin/Authorized user.
$notify_actions['student_account_created'] = array(
    'specific' => array(
        'message' => 'Your account has been created in SGlearningapp.',
        'link' => 'login'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Account of student named {student_name} has been created.',
            'link' => 'admin/user/?&filter=active&offset=1'
        ),
        'multiple' => array(
            'message' => '{number} accounts has been created in SGlearningapp.',
            'link' => 'admin/user/?&filter=active&offset=1'
        )
    ),
    'opposite_action' => array()
);
//Accounts imported by Admin/Authorized user.
$notify_actions['students_imported'] = array(
    'specific' => array(
        'message' => 'Your account has been created in SGlearningapp.',
        'link' => 'dashboard'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Account of student named {student_name} has been created.',
            'link' => 'admin/user/?&filter=active&offset=1'
        ),
        'multiple' => array(
            'message' => '{number} accounts has been created in SGlearningapp.',
            'link' => 'admin/user/?&filter=active&offset=1'
        )
    ),
    'opposite_action' => array()
);
//Password reset by Admin/Authorized user.
$notify_actions['student_password_reset'] = array(
    'specific' => array(
        'message' => 'Your password has been reset.',
        'link' => 'login'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Password of student named {student_name} has been reset.',
            'link' => 'admin/user/?&filter=active&offset=1'
        ),
        'multiple' => array(
            'message' => '{number} account password has been reset in SGlearningapp.',
            'link' => 'admin/user/?&filter=active&offset=1'
        )
    ),
    'opposite_action' => array(),
);
//Account deleted by Admin/Authorized user.
$notify_actions['student_account_deleted'] = array(
    'specific' => array(
        'message' => 'Your account has been deleted.',
        'link' => 'login'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Account of student named {student_name} has been deleted.',
            'link' => 'admin/user/?&filter=deleted&offset=1'
        ),
        'multiple' => array(
            'message' => '{number} account has been deleted in SGlearningapp.',
            'link' => 'admin/user/?&filter=deleted&offset=1'
        )
    ),
    'opposite_action' => array(),
);

/*======================================================================================*/

//Enrollment has been approved by Admin/Authorized user. {Normal,Bulk}
$notify_actions['course_subscription_approved'] = array(
    'specific' => array(
        'message' => 'Your enrollment to course {course_name} has been approved.',
        'link' => 'dashboard/courses'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Enrollment for course {course_name} has been granted for student named {student_name}.',
            'link' => 'admin/course/users/{course_id}/?&filter=active&offset=1'
        ),
        'multiple' => array(
            'message' => 'Enrollment for course {course_name} has been granted for {number} students.',
            'link' => 'admin/course/users/{course_id}/?&filter=active&offset=1'
        )
    ),
    'opposite_action' => array(),
);
//Enrollment has been suspended by Admin/Authorized user.  {Normal,Bulk}
$notify_actions['course_subscription_suspended'] = array(
    'specific' => array(
        'message' => 'Your enrollment to course {course_name} has been suspended.',
        'link' => 'dashboard/courses'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Enrollment for course {course_name} has been suspended for student named {student_name}.',
            'link' => 'admin/course/users/{course_id}/?&filter=suspended&offset=1'
        ),
        'multiple' => array(
            'message' => 'Enrollment for course {course_name} has been suspended for {number} students.',
            'link' => 'admin/course/users/{course_id}/?&filter=suspended&offset=1'
        )
    ),
    'opposite_action' => array(),
);
//Enrollment has been removed by Admin/Authorized user. {Normal,Bulk}
$notify_actions['course_subscription_removed'] = array(
    'specific' => array(
        'message' => 'Your enrollment to course {course_name} has been removed.',
        'link' => 'dashboard/courses'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Enrollment for course {course_name} has been removed for student named {student_name}.',
            'link' => 'admin/course/users/{course_id}/?&filter=all&offset=1'
        ),
        'multiple' => array(
            'message' => 'Enrollment for course {course_name} has been removed for {number} students.',
            'link' => 'admin/course/users/{course_id}/?&filter=all&offset=1'
        )
    ),
    'opposite_action' => array(),
);
//Enrollment has been made by Admin/Authorized user. {Normal,Bulk}
$notify_actions['course_subscribed'] = array(
    'specific' => array(
        'message' => 'You have been enrolled to course {course_name}.',
        'link' => 'dashboard/courses'
    ),
    'common' => array(
        'single' => array(
            'message' => 'A student named {student_name} has subscribed to the course {course_name}.',
            'link' => 'admin/course/users/{course_id}/?&filter=active&offset=1'
        ),
        'multiple' => array(
            'message' => '{number} subscriptions to course named {course_name}.',
            'link' => 'admin/course/users/{course_id}/?&filter=active&offset=1'
        )
    ),
    'opposite_action' => array(),
);
//Enrollment has been made by Admin/Authorized user. {Normal,Bulk}
$notify_actions['bundle_course_subscribed'] = array(
    'specific' => array(
        'message' => 'You have been enrolled to course {course_name}.',
        'link' => '{bundle_url}'
    ),
    'common' => array(
        'single' => array(
            'message' => 'A student named {student_name} has subscribed to the course {course_name}.',
            'link' => 'admin/course/users/{course_id}/?&filter=active&offset=1'
        ),
        'multiple' => array(
            'message' => '{number} subscriptions to course named {course_name}.',
            'link' => 'admin/course/users/{course_id}/?&filter=active&offset=1'
        )
    ),
    'opposite_action' => array(),
);
//Enrollment validity has been changed by Admin/Authorized user. {Normal,Bulk}
$notify_actions['course_subscription_validity_changed'] = array(
    'specific' => array(
        'message' => 'Your validity to course {course_name} has been changed.',
        'link' => 'dashboard'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Enrollment validity for course {course_name} has been changed for student named {student_name}.',
            'link' => 'admin/course/users/{course_id}/?&filter=active&offset=1'
        ),
        'multiple' => array(
            'message' => '{number} enrollment validity has been changed in course {course_name}.',
            'link' => 'admin/course/users/{course_id}/?&filter=active&offset=1'
        )
    ),
    'opposite_action' => array(),
);
//Access to forum has blocked by Admin/Authorized user.
$notify_actions['forum_blocked'] = array(
    'specific' => array(
        'message' => 'Your access to forum has been blocked in course {course_name}.',
        'link' => 'course/dashboard/{course_id}?tab=qa'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Forum access for course {course_name} has been blocked for student named {student_name}.',
            'link' => 'admin/course/users/{course_id}/?&filter=all&offset=1'
        ),
        'multiple' => array(
            'message' => '{number} students has been blocked from forum in course {course_name}.',
            'link' => 'admin/course/users/{course_id}/?&filter=all&offset=1'
        )
    ),
    'opposite_action' => array(),
);

/*=======================================================================================*/

//New event has been created by Admin/Authorized user.
$notify_actions['event_created'] = array(
    'specific' => array(
        'message' => 'A new event named {event_name} has been created.',
        'link' => 'events/event/{event_id_hash}'
    ),
    'common' => array(
        'single' => array(
            'message' => 'A new event named {event_name} has been created.',
            'link' => 'admin/event/'
        ),
        'multiple' => array(
            'message' => '{number} event has been created.',
            'link' => 'admin/event/'
        )
    ),
    'opposite_action' => array(),
);
//Event has been deleted by Admin/Authorized user.
$notify_actions['event_deleted'] = array(
    'specific' => array(
        'message' => 'Event named {event_name} has been deleted.',
        'link' => 'dashboard'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Event named {event_name} has been deleted.',
            'link' => 'admin'
        ),
        'multiple' => array(
            'message' => '{number} event has been deleted.',
            'link' => 'admin'
        )
    ),
    'opposite_action' => array(),
);
//Event reminder for linked users.
$notify_actions['event_reminder'] = array(
    'specific' => array(
        'message' => 'Reminder - Event named {event_name} has been arriving on {event_date} at {event_time}.',
        'link' => 'dashboard'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Reminder - Event named {event_name} has been arriving on {event_date} at {event_time}.',
            'link' => 'admin'
        ),
        'multiple' => array(
            'message' => 'Reminder - Event named {event_name} has been arriving on {event_date} at {event_time}.',
            'link' => 'admin'
        )
    ),
    'opposite_action' => array(),
);

/*=======================================================================================*/

//Course has been rated by student.
$notify_actions['course_rated'] = array(
    'specific' => array(
        'message' => 'You have been rated a course named {course_name} with rating {rating}.',
        'link' => 'dashboard'
    ),
    'common' => array(
        'single' => array(
            'message' => 'A student named {student_name} has rated course {course_name}.',
            'link' => 'admin/course/reviews/{course_id}'
        ),
        'multiple' => array(
            'message' => '{number} students have been rated the {course_name}.',
            'link' => 'admin/course/reviews/{course_id}'
        )
    ),
    'opposite_action' => array(),
);

//Bundle has been rated by student.
$notify_actions['bundle_rated'] = array(
    'specific' => array(
        'message' => 'You have been rated a bundle named {bundle_name} with rating {rating}.',
        'link' => '{bundle_slug}'
    ),
    'common' => array(
        'single' => array(
            'message' => 'A student named {student_name} has rated the bundle {bundle_name}.',
            'link' => 'admin/bundle/reviews/{bundle_id}'
        ),
        'multiple' => array(
            'message' => '{number} students have been rated the bundle {bundle_name}.',
            'link' => 'admin/bundle/reviews/{bundle_id}'
        )
    ),
    'opposite_action' => array(),
);

//Course result has been reset by Admin/Authorized user.
$notify_actions['course_subscription_result_reset'] = array(
    'specific' => array(
        'message' => 'Your result has been reset in course named {course_name}.',
        'link' => 'course/dashboard/{course_id}'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Result of a student named {student_name} under course {course_name} has been reset.',
            'link' => 'admin/course/users/{course_id}'
        ),
        'multiple' => array(
            'message' => '{number} result reset under course {course_name}.',
            'link' => 'admin/course/users/{course_id}'
        )
    ),
    'opposite_action' => array(),
);

/*=======================================================================================*/

//Announcement has been made under a course by Admin/Authorized user.
$notify_actions['announcement_created'] = array(
    'specific' => array(
        'message' => 'An announcement has been made under course {course_name}.',
        'link' => 'course/dashboard/{course_id}?tab=anouncements'
    ),
    'common' => array(
        'single' => array(
            'message' => 'An announcement has been made under course {course_name}.',
            'link' => 'admin/course/announcement/{course_id}'
        ),
        'multiple' => array(
            'message' => '{number} of announcement has been made under course {course_name}.',
            'link' => 'admin/course/announcement/{course_id}'
        )
    ),
    'opposite_action' => array(),
);

/*=======================================================================================*/

//Faculty has been added to a course by Admin/Authorized user.
$notify_actions['faculty_assigned_to_course'] = array(
    'specific' => array(
        'message' => 'You have been assigned to a course named {course_name}.',
        'link' => 'admin/course_settings/advanced/{course_id}'
    ),
    'common' => array(
        'single' => array(
            'message' => 'A faculty has been assigned under course {course_name}.',
            'link' => 'admin/course_settings/advanced/{course_id}'
        ),
        'multiple' => array(
            'message' => '{number} faculties has been assigned under course {course_name}.',
            'link' => 'admin/course_settings/advanced/{course_id}'
        )
    ),
    'opposite_action' => array(),
);
//Faculty has deleted by Admin/Authorized user. {Normal,Bulk}
$notify_actions['faculty_account_deleted'] = array(
    'specific' => array(
        'message' => 'Your account has been deleted.',
        'link' => 'admin'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Faculty account named {faculty_name} has been removed.',
            'link' => 'admin/faculties'
        ),
        'multiple' => array(
            'message' => '{number} faculties has been removed.',
            'link' => 'admin/faculties'
        )
    ),
    'opposite_action' => array(),
);
//Faculty has activated by Admin/Authorized user. {Normal,Bulk}
$notify_actions['faculty_account_activated'] = array(
    'specific' => array(
        'message' => 'Your account has been activated.',
        'link' => 'login'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Faculty account named {faculty_name} has been activated.',
            'link' => 'admin/faculties'
        ),
        'multiple' => array(
            'message' => '{number} faculties has been activated.',
            'link' => 'admin/faculties'
        )
    ),
    'invertible' => true,
    'opposite_action' => array('faculty_account_deactivated'),
);
//Faculty has deactivated by Admin/Authorized user. {Normal,Bulk}
$notify_actions['faculty_account_deactivated'] = array(
    'specific' => array(
        'message' => 'Your account has been deactivated.',
        'link' => 'login'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Faculty account named {faculty_name} has been deactivated.',
            'link' => 'admin/faculties'
        ),
        'multiple' => array(
            'message' => '{number} faculties has been deactivated.',
            'link' => 'admin/faculties'
        )
    ),
    'invertible' => true,
    'opposite_action' => array('faculty_account_activated'),
);

/*=======================================================================================*/
$notify_actions['live_reminder'] = array(
    'specific' => array(
        'message' => 'Reminder - Live named {live_name} has been scheduled on {live_date} at {live_time} in course {course_name}.Don\'t miss it!!!',
        'link' => 'dashboard'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Reminder - Live named {live_name} has been scheduled on {live_date} at {live_time} in course {course_name}.',
            'link' => 'admin'
        ),
        'multiple' => array(
            'message' => 'Reminder - Live named {live_name} has been scheduled on {live_date} at {live_time} in course {course_name}.',
            'link' => 'admin'
        )
    ),
    'opposite_action' => array(),
);
//Live scheduled under a course by Admin/Authorized user.
$notify_actions['live_scheduled'] = array(
    'specific' => array(
        'message' => 'A live named {live_name} has been scheduled under course {course_name}.',
        'link' => 'course/dashboard/{course_id}'
    ),
    'common' => array(
        'single' => array(
            'message' => 'A live named {live_name} has been scheduled under course {course_name}.',
            'link' => 'admin/course/basic/{course_id}'
        ),
        'multiple' => array(
            'message' => 'A live named {live_name} has been scheduled under course {course_name}.',
            'link' => 'admin/course/basic/{course_id}'
        )
    ),
    'opposite_action' => array(),
);
$notify_actions['lecture_created'] = array(
    'specific' => array(
        'message' => 'A new lecture named  <b> {lecture_name} </b> has been added under course <b> {course_name}.',
        'link' => 'course/dashboard/{course_id}'
    ),
    'common' => array(
        'single' => array(
            'message' => 'A new lecture named <b> {lecture_name} </b> has been added under course <b> {course_name}.',
            'link' => 'admin/course/basic/{course_id}'
        ),
        'multiple' => array(
            'message' => 'A new lecture named <b> {lecture_name} </b> has been added under course <b> {course_name}.',
            'link' => 'admin/course/basic/{course_id}'
        )
    ),
    'opposite_action' => array(),
);
/*=======================================================================================*/

//Quiz notification to student.
$notify_actions['quiz_notify'] = array(
    'specific' => array(
        'message' => 'Last submission date of the quiz {quiz_name} under the course {course_name} is {date}.',
        'link' => 'course/dashboard/{course_id}?tab=quiz'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Last submission date of the quiz {quiz_name} under the course {course_name} is {date}.',
            'link' => 'course/dashboard/{course_id}?tab=quiz'
        ),
        'multiple' => array(
            'message' => 'Last submission date of the quiz {quiz_name} under the course {course_name} is {date}.',
            'link' => 'course/dashboard/{course_id}?tab=quiz'
        )
    ),
    'opposite_action' => array(),
);
//Assignment notification to student.
$notify_actions['assignment_notify'] = array(
    'specific' => array(
        'message' => 'Last submission date of the assignment {assignment_name} under the course {course_name} is {date}.',
        'link' => 'course/dashboard/{course_id}'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Last submission date of the assignment {assignment_name} under the course {course_name} is {date}.',
            'link' => 'admin/course/basic/{course_id}'
        ),
        'multiple' => array(
            'message' => 'Last submission date of the assignment {assignment_name} under the course {course_name} is {date}.',
            'link' => 'admin/course/basic/{course_id}'
        )
    ),
    'opposite_action' => array(),
);

//Assignment submission.
$notify_actions['assignment_submitted'] = array(
    'specific' => array(
        'message' => 'You have submitted an assignment named {assignment_name} under course {course_name}.',
        'link' => 'course/dashboard/{course_id}?tab=assignments'
    ),
    'common' => array(
        'single' => array(
            'message' => 'An assignment named {assignment_name} has been submitted under course {course_name}.',
            'link' => 'admin/report/assignment?course_id={course_id}'
        ),
        'multiple' => array(
            'message' => '{number} submission of an assignment named {assignment_name} under course {course_name}.',
            'link' => 'admin/report/assignment?course_id={course_id}'
        )
    ),
    'opposite_action' => array(),
);
//Quiz submission.
$notify_actions['quiz_submitted'] = array(
    'specific' => array(
        'message' => 'You have submitted a quiz named {quiz_name} under course {course_name}.',
        'link' => 'login'
    ),
    'common' => array(
        'single' => array(
            'message' => 'A quiz named {quiz_name} has been submitted under course {course_name}.',
            'link' => 'admin/report/assessments/?course_id={course_id}'
        ),
        'multiple' => array(
            'message' => '{number} submission of a quiz named {quiz_name} under course {course_name}.',
            'link' => 'admin/report/assessments/?course_id={course_id}'
        )
    ),
    'opposite_action' => array(),
);
//Survey submission.
$notify_actions['survey_submitted'] = array(
    'specific' => array(
        'message' => 'You have submitted a survey named {survey_name} under course {course_name}.',
        'link' => 'login'
    ),
    'common' => array(
        'single' => array(
            'message' => 'A survey named {survey_name} has been submitted under course {course_name}.',
            'link' => 'admin/report/survey_report/{survey_lecture_id}'
        ),
        'multiple' => array(
            'message' => '{number} submission of a survey named {survey_name} under course {course_name}.',
            'link' => 'admin/report/survey_report/{survey_lecture_id}'
        )
    ),
    'opposite_action' => array(),
);

//Assignment graded.
$notify_actions['assignment_graded'] = array(
    'specific' => array(
        'message' => 'Your submission of assignment named {assignment_name} under course {course_name} has been graded {grade}.',
        'link' => 'course/dashboard/{course_id}?tab=assignments'
    ),
    'common' => array(
        'single' => array(
            'message' => 'A grading under assignment named {assignment_name} under course {course_name}.',
            'link' => 'admin/course/basic/{course_id}'
        ),
        'multiple' => array(
            'message' => '{number} grading under assignment named {assignment_name} under course {course_name}.',
            'link' => 'admin/course/basic/{course_id}'
        )
    ),
    'opposite_action' => array(),
);
//Quiz graded.
$notify_actions['quiz_graded'] = array(
    'specific' => array(
        'message' => 'Your submission of quiz named {quiz_name} under course {course_name} has been graded {grade}.',
        'link' => 'course/dashboard/{course_id}?tab=quiz'
    ),
    'common' => array(
        'single' => array(
            'message' => 'A grading under quiz named {quiz_name} under course {course_name}.',
            'link' => 'admin/course/basic/{course_id}'
        ),
        'multiple' => array(
            'message' => '{number} grading under quiz named {quiz_name} under course {course_name}.',
            'link' => 'admin/course/basic/{course_id}'
        )
    ),
    'opposite_action' => array(),
);

//Question created.
$notify_actions['question_created'] = array(
    'specific' => array(
        'message' => '{user_name} created the question {question_name} under the course {course_name}.',
        'link' => 'course/dashboard/{course_id}?tab=qa'
    ),
    'common' => array(
        'single' => array(
            'message' => '{user_name} created the question {question_name} under the course {course_name}.',
            'link' => 'admin/course/discussion/{course_id}'
        ),
        'multiple' => array(
            'message' => '{number} created the question {question_name} under the course {course_name}.',
            'link' => 'admin/course/discussion/{course_id}'
        )
    ),
    'opposite_action' => array(),
);

//Question answered.
$notify_actions['question_answered'] = array(
    'specific' => array(
        'message' => '{user_name} answered the question {question_name} under the course {course_name}.',
        'link' => 'course/dashboard/{course_id}?tab=qa'
    ),
    'common' => array(
        'single' => array(
            'message' => '{user_name} answered the question {question_name} under the course {course_name}.',
            'link' => 'admin/course/discussion/{course_id}'
        ),
        'multiple' => array(
            'message' => '{number} answered the question {question_name} under the course {course_name}.',
            'link' => 'admin/course/discussion/{course_id}'
        )
    ),
    'opposite_action' => array(),
);

//Post answered.
$notify_actions['post_answered'] = array(
    'specific' => array(
        'message' => '<b>{username}</b> answerd the question <b>{question_name}</b>.',
        'link' => 'course/dashboard/{course_id}?tab=qa'
    ),
    'common' => array(
        'single' => array(
            'message' => '<b>{username}</b> answerd the question <b>{question_name}</b>.',
            'link' => 'admin/course/discussion/{course_id}'
        ),
        'multiple' => array(
            'message' => '<b>{number}</b> answer under the question <b>{question_name}</b>.',
            'link' => 'admin/course/discussion/{course_id}'
        )
    ),
    'opposite_action' => array(),
);

$notify_actions['my_post_answered'] = array(
    'specific' => array(
        'message' => '<b>{username}</b> answered your question <b>{question_name}</b>.',
        'link' => 'course/dashboard/{course_id}?tab=qa'
    ),
    'common' => array(
        'single' => array(
            'message' => '<b>{username}</b> answered your question <b>{question_name}</b>.',
            'link' => 'admin/course/discussion/{course_id}'
        ),
        'multiple' => array(
            'message' => '{number} answer under your question <b>{question_name}</b>.',
            'link' => 'admin/course/discussion/{course_id}'
        )
    ),
    'opposite_action' => array(),
);

$notify_actions['student_paid_to_course'] = array(
    'specific' => array(
        'message' => 'You have made payment for the course {course_name}.',
        'link' => 'dashboard'
    ),
    'common' => array(
        'single' => array(
            'message' => 'Student named {student_name} has made payment for the course {course_name}.',
            'link' => 'admin/orders'
        ),
        'multiple' => array(
            'message' => '{number} new payment has been done.',
            'link' => 'admin/orders'
        )
    ),
    'opposite_action' => array(),
);
//Enrollment has been made by Admin/Authorized user. {Normal,Bulk}
$notify_actions['bundle_subscribed'] = array(
    'specific' => array(
        'message' => 'You have been enrolled to a bundle {bundle_name}.',
        'link' => 'dashboard/courses'
    ),
    'common' => array(
        'single' => array(
            'message' => 'A student named {student_name} has subscribed to the bundle {bundle_name}.',
            'link' => 'admin/bundle/users/{bundle_id}/?&filter=active&offset=1'
        ),
        'multiple' => array(
            'message' => '{number} subscriptions to bundle named {bundle_name}.',
            'link' => 'admin/bundle/users/{bundle_id}/?&filter=active&offset=1'
        )
    ),
    'opposite_action' => array(),
);

$notify_actions['purchase_notify'] = array(
    'specific' => array(
        'message' => 'You have puchased a {item_type} {item_name}.',
        'link' => 'dashboard/courses'
    ),
    'common' => array(
        'single' => array(
            'message' => 'A student named {student_name} has purchased the {item_type} {item_name}.',
            'link' => 'admin/orders/'
        ),
        'multiple' => array(
            'message' => '{number} students have purchased bundle named {item_type} {item_name}.',
            'link' => 'admin/orders/'
        )
    ),
    'opposite_action' => array(),
);
$config['notify_actions'] = $notify_actions;
