<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Categories;
use App\Models\Comment;
use App\Models\Department;
use App\Models\Idea;
use App\Models\IdeaDocument;
use App\Models\Reaction;
use App\Models\ReportedIdea;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $roles = [
            ['role_name' => 'admin'],
            ['role_name' => 'manager'],
            ['role_name' => 'coordinator'],
            ['role_name' => 'staff'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }

        // Create Departments
        $departments = [
            ['department_name' => 'Computer Science'],
            ['department_name' => 'Engineering'],
            ['department_name' => 'Business Administration'],
            ['department_name' => 'Mathematics'],
            ['department_name' => 'Literature & Languages'],
            ['department_name' => 'Psychology'],
            ['department_name' => 'Physics'],
            ['department_name' => 'Biology'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }

        // Create Categories
        $categories = [
            ['category_name' => 'Process Improvement'],
            ['category_name' => 'Technology Enhancement'],
            ['category_name' => 'Student Services'],
            ['category_name' => 'Campus Culture'],
            ['category_name' => 'Academic Innovation'],
            ['category_name' => 'Faculty Development'],
            ['category_name' => 'Resource Optimization'],
            ['category_name' => 'Sustainability'],
        ];

        foreach ($categories as $category) {
            Categories::create($category);
        }

        // Create Academic Years
        $academicYears = [
            [
                'academic_name' => 'Spring 2025',
                'start_date' => '2025-01-15',
                'end_date' => '2025-06-30',
                'closure_date' => '2025-05-15',
                'final_closure_date' => '2025-06-15',
                'remark' => 'Current Academic Term',
            ],
            [
                'academic_name' => 'Fall 2024',
                'start_date' => '2024-08-15',
                'end_date' => '2024-12-31',
                'closure_date' => '2024-11-15',
                'final_closure_date' => '2024-12-15',
                'remark' => 'Previous Academic Term',
            ],
            [
                'academic_name' => 'Spring 2024',
                'start_date' => '2024-01-15',
                'end_date' => '2024-06-30',
                'closure_date' => '2024-05-15',
                'final_closure_date' => '2024-06-15',
                'remark' => 'Past Academic Term',
            ],
            [
                'academic_name' => 'Fall 2023',
                'start_date' => '2023-08-15',
                'end_date' => '2023-12-31',
                'closure_date' => '2023-11-15',
                'final_closure_date' => '2023-12-15',
                'remark' => 'Archive Academic Term',
            ],
        ];

        foreach ($academicYears as $year) {
            AcademicYear::create($year);
        }

        // Create Users with different roles
        $users = [
            [
                'user_name' => 'John Doe',
                'email' => 'john.doe@university.edu',
                'password' => Hash::make('password'),
                'phone_no' => '1234567890',
                'role_id' => 1, // Admin
                'department_id' => 1,
            ],
            [
                'user_name' => 'Sarah Wilson',
                'email' => 'sarah.wilson@university.edu',
                'password' => Hash::make('password'),
                'phone_no' => '1234567891',
                'role_id' => 2, // Manager
                'department_id' => 2,
            ],
            [
                'user_name' => 'Michael Chen',
                'email' => 'michael.chen@university.edu',
                'password' => Hash::make('password'),
                'phone_no' => '1234567892',
                'role_id' => 3, // Coordinator
                'department_id' => 3,
            ],
            [
                'user_name' => 'Emma Davis',
                'email' => 'emma.davis@university.edu',
                'password' => Hash::make('password'),
                'phone_no' => '1234567893',
                'role_id' => 4, // Staff
                'department_id' => 4,
            ],
            [
                'user_name' => 'Alex Thompson',
                'email' => 'alex.thompson@university.edu',
                'password' => Hash::make('password'),
                'phone_no' => '1234567894',
                'role_id' => 4, // Staff
                'department_id' => 5,
            ],
            [
                'user_name' => 'Lisa Park',
                'email' => 'lisa.park@university.edu',
                'password' => Hash::make('password'),
                'phone_no' => '1234567895',
                'role_id' => 4, // Staff
                'department_id' => 6,
            ],
            [
                'user_name' => 'David Lee',
                'email' => 'david.lee@university.edu',
                'password' => Hash::make('password'),
                'phone_no' => '1234567896',
                'role_id' => 4, // Staff
                'department_id' => 7,
            ],
            [
                'user_name' => 'Sophia Rodriguez',
                'email' => 'sophia.rodriguez@university.edu',
                'password' => Hash::make('password'),
                'phone_no' => '1234567897',
                'role_id' => 4, // Staff
                'department_id' => 8,
            ],
            [
                'user_name' => 'Robert Johnson',
                'email' => 'robert.johnson@university.edu',
                'password' => Hash::make('password'),
                'phone_no' => '1234567898',
                'role_id' => 2, // Manager
                'department_id' => 1,
            ],
            [
                'user_name' => 'Jennifer Williams',
                'email' => 'jennifer.williams@university.edu',
                'password' => Hash::make('password'),
                'phone_no' => '1234567899',
                'role_id' => 3, // Coordinator
                'department_id' => 5,
            ],
            [
                'user_name' => 'Daniel Brown',
                'email' => 'daniel.brown@university.edu',
                'password' => Hash::make('password'),
                'phone_no' => '1234567810',
                'role_id' => 4, // Staff
                'department_id' => 2,
            ],
            [
                'user_name' => 'Michelle Garcia',
                'email' => 'michelle.garcia@university.edu',
                'password' => Hash::make('password'),
                'phone_no' => '1234567811',
                'role_id' => 4, // Staff
                'department_id' => 3,
            ],
            [
                'user_name' => 'James Miller',
                'email' => 'james.miller@university.edu',
                'password' => Hash::make('password'),
                'phone_no' => '1234567812',
                'role_id' => 4, // Staff
                'department_id' => 6,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        // Create Ideas
        $ideas = [
            [
                'title' => 'Implement Automated Testing Framework',
                'content' => 'Propose implementing a comprehensive automated testing framework to improve code quality and reduce manual testing time in university coding courses. This will include setting up CI/CD pipelines, automated unit tests, and integration tests.',
                'is_anonymous' => false,
                'view_count' => 45,
                'popularity' => 12,
                'user_id' => 4, // Emma Davis
                'academic_year_id' => 1,
                'category_id' => 2,
                'remark' => 'Proposed by Mathematics Department',
                'report_count' => 0,
                'is_hidden' => false,
            ],
            [
                'title' => 'Improve Student Onboarding Process',
                'content' => 'Suggest improvements to the current student onboarding process to make it more efficient and engaging. This includes digital documentation, interactive orientation modules, and peer mentorship programs.',
                'is_anonymous' => false,
                'view_count' => 38,
                'popularity' => 15,
                'user_id' => 5, // Alex Thompson
                'academic_year_id' => 1,
                'category_id' => 1,
                'remark' => 'Proposed by Literature & Languages Department',
                'report_count' => 0,
                'is_hidden' => false,
            ],
            [
                'title' => 'Enhance Student Support System',
                'content' => 'Propose implementing a new student support system to better track and resolve student issues. This will include AI-powered inquiry routing, automated response suggestions, and student satisfaction tracking.',
                'is_anonymous' => false,
                'view_count' => 52,
                'popularity' => 18,
                'user_id' => 6, // Lisa Park
                'academic_year_id' => 1,
                'category_id' => 3,
                'remark' => 'Proposed by Psychology Department',
                'report_count' => 1,
                'is_hidden' => true,
            ],
            [
                'title' => 'Green Campus Initiative',
                'content' => 'Propose implementing eco-friendly practices on campus, including paperless operations, energy-efficient lighting, and recycling programs to reduce our carbon footprint.',
                'is_anonymous' => false,
                'view_count' => 42,
                'popularity' => 14,
                'user_id' => 8, // Sophia Rodriguez
                'academic_year_id' => 1,
                'category_id' => 8,
                'remark' => 'Proposed by Biology Department',
                'report_count' => 0,
                'is_hidden' => false,
            ],
            [
                'title' => 'Faculty Skill Development Program',
                'content' => 'Suggest creating a comprehensive skill development program that includes online courses, workshops, and certification opportunities to help faculty members grow in their academic careers.',
                'is_anonymous' => false,
                'view_count' => 35,
                'popularity' => 10,
                'user_id' => 7, // David Lee
                'academic_year_id' => 1,
                'category_id' => 6,
                'remark' => 'Proposed by Physics Department',
                'report_count' => 0,
                'is_hidden' => false,
            ],
            [
                'title' => 'Cross-Departmental Collaboration Platform',
                'content' => 'Develop a platform that facilitates collaboration between different academic departments, allowing for better knowledge sharing, resource allocation, and research project coordination.',
                'is_anonymous' => true,
                'view_count' => 29,
                'popularity' => 8,
                'user_id' => 11, // Daniel Brown
                'academic_year_id' => 1,
                'category_id' => 1,
                'remark' => 'Anonymous submission',
                'report_count' => 0,
                'is_hidden' => false,
            ],
            [
                'title' => 'Implement Flexible Learning Arrangements',
                'content' => 'Propose a flexible learning arrangement policy that allows students to attend classes remotely or adjust their schedules to improve work-life balance while maintaining academic performance.',
                'is_anonymous' => false,
                'view_count' => 67,
                'popularity' => 22,
                'user_id' => 12, // Michelle Garcia
                'academic_year_id' => 1,
                'category_id' => 4,
                'remark' => 'Proposed by Business Administration Department',
                'report_count' => 0,
                'is_hidden' => false,
            ],
            [
                'title' => 'Student Feedback Analysis System',
                'content' => 'Develop an automated system to collect, categorize, and analyze student feedback from various channels to identify trends and improvement opportunities for courses and campus services.',
                'is_anonymous' => false,
                'view_count' => 41,
                'popularity' => 15,
                'user_id' => 13, // James Miller
                'academic_year_id' => 1,
                'category_id' => 3,
                'remark' => 'Proposed by Psychology Department',
                'report_count' => 0,
                'is_hidden' => false,
            ],
            [
                'title' => 'AI-Powered Research Trend Analysis',
                'content' => 'Implement AI algorithms to analyze research trends, academic publications, and funding opportunities to provide actionable insights for faculty research and departmental focus areas.',
                'is_anonymous' => false,
                'view_count' => 56,
                'popularity' => 19,
                'user_id' => 10, // Jennifer Williams
                'academic_year_id' => 1,
                'category_id' => 5,
                'remark' => 'Proposed by Literature & Languages Department',
                'report_count' => 0,
                'is_hidden' => false,
            ],
            [
                'title' => 'Sustainable Campus Resource Management',
                'content' => 'Develop a framework for evaluating and improving the sustainability of our campus resources, including energy assessments, carbon footprint reduction, and ethical procurement practices.',
                'is_anonymous' => false,
                'view_count' => 38,
                'popularity' => 13,
                'user_id' => 8, // Sophia Rodriguez
                'academic_year_id' => 1,
                'category_id' => 8,
                'remark' => 'Proposed by Biology Department',
                'report_count' => 0,
                'is_hidden' => false,
            ],
            [
                'title' => 'Wellness Program for Students and Faculty',
                'content' => 'Create a comprehensive wellness program that includes physical health initiatives, mental health support, and work-life balance practices to improve campus wellbeing and academic productivity.',
                'is_anonymous' => false,
                'view_count' => 61,
                'popularity' => 25,
                'user_id' => 3, // Michael Chen
                'academic_year_id' => 1,
                'category_id' => 4,
                'remark' => 'Proposed by Business Administration Department',
                'report_count' => 0,
                'is_hidden' => false,
            ],
            [
                'title' => 'Digital Documentation System',
                'content' => 'Implement a digital documentation system to replace paper-based processes, improving efficiency, searchability, and environmental sustainability for university records and forms.',
                'is_anonymous' => false,
                'view_count' => 32,
                'popularity' => 11,
                'user_id' => 4, // Emma Davis
                'academic_year_id' => 2,
                'category_id' => 7,
                'remark' => 'Proposed by Mathematics Department',
                'report_count' => 0,
                'is_hidden' => false,
            ],
            [
                'title' => 'Advanced Cybersecurity Training Program',
                'content' => 'Develop an advanced cybersecurity training program to educate students and faculty about digital threats, data protection, and best practices to prevent security breaches of university systems.',
                'is_anonymous' => false,
                'view_count' => 48,
                'popularity' => 16,
                'user_id' => 1, // John Doe
                'academic_year_id' => 2,
                'category_id' => 2,
                'remark' => 'Proposed by Computer Science Department',
                'report_count' => 0,
                'is_hidden' => false,
            ],
            [
                'title' => 'Community Engagement Initiative',
                'content' => 'Create a structured program for students and faculty to engage with local communities through volunteering, mentorship, and charitable activities to strengthen our university\'s social responsibility.',
                'is_anonymous' => false,
                'view_count' => 37,
                'popularity' => 14,
                'user_id' => 10, // Jennifer Williams
                'academic_year_id' => 2,
                'category_id' => 4,
                'remark' => 'Proposed by Literature & Languages Department',
                'report_count' => 0,
                'is_hidden' => false,
            ],
            [
                'title' => 'Academic Knowledge Base Enhancement',
                'content' => 'Improve our university knowledge base system with better categorization, search functionality, and user-generated content to facilitate knowledge sharing and reduce repeated inquiries.',
                'is_anonymous' => true,
                'view_count' => 27,
                'popularity' => 9,
                'user_id' => 6, // Lisa Park
                'academic_year_id' => 2,
                'category_id' => 6,
                'remark' => 'Anonymous submission',
                'report_count' => 0,
                'is_hidden' => false,
            ],
        ];

        foreach ($ideas as $idea) {
            Idea::create($idea);
        }

        // Create Comments
        $comments = [
            [
                'desc' => 'Great idea! This would significantly improve our testing efficiency and reduce bugs in production.',
                'is_anonymous' => false,
                'user_id' => 2, // sarah_wilson
                'idea_id' => 1,
            ],
            [
                'desc' => 'We should consider integrating this with our existing Jenkins pipeline.',
                'is_anonymous' => false,
                'user_id' => 9, // robert_johnson
                'idea_id' => 1,
            ],
            [
                'desc' => 'Can we get more details on the estimated implementation cost?',
                'is_anonymous' => false,
                'user_id' => 1, // john_doe
                'idea_id' => 1,
            ],
            [
                'desc' => 'I agree with this proposal. The current onboarding process needs improvement, and digital documentation would make it much smoother.',
                'is_anonymous' => false,
                'user_id' => 3, // michael_chen
                'idea_id' => 2,
            ],
            [
                'desc' => 'As a recent hire, I found the onboarding quite disorganized. This would be a welcome change.',
                'is_anonymous' => true,
                'user_id' => 11, // daniel_brown
                'idea_id' => 2,
            ],
            [
                'desc' => 'We should consider the cost implications of this proposal, but the benefits seem worth the investment.',
                'is_anonymous' => false,
                'user_id' => 1, // john_doe
                'idea_id' => 3,
            ],
            [
                'desc' => 'This aligns perfectly with our sustainability goals. I can help create a detailed implementation plan.',
                'is_anonymous' => false,
                'user_id' => 2, // sarah_wilson
                'idea_id' => 4,
            ],
            [
                'desc' => 'I would love to participate in this program. It would help me stay updated with the latest technologies.',
                'is_anonymous' => false,
                'user_id' => 4, // emma_davis
                'idea_id' => 5,
            ],
            [
                'desc' => 'This could really help us break down silos between departments.',
                'is_anonymous' => false,
                'user_id' => 7, // david_lee
                'idea_id' => 6,
            ],
            [
                'desc' => 'Have we considered Microsoft Teams for this purpose? It might be a more cost-effective solution.',
                'is_anonymous' => false,
                'user_id' => 1, // john_doe
                'idea_id' => 6,
            ],
            [
                'desc' => 'As a working parent, I fully support this initiative!',
                'is_anonymous' => false,
                'user_id' => 8, // sophia_rodriguez
                'idea_id' => 7,
            ],
            [
                'desc' => 'We need to ensure we have the right metrics to measure effectiveness of this policy.',
                'is_anonymous' => false,
                'user_id' => 3, // michael_chen
                'idea_id' => 7,
            ],
            [
                'desc' => 'Would this integrate with our existing CRM?',
                'is_anonymous' => false,
                'user_id' => 5, // alex_thompson
                'idea_id' => 8,
            ],
            [
                'desc' => 'This sounds promising, but we need to ensure data privacy compliance.',
                'is_anonymous' => false,
                'user_id' => 9, // robert_johnson
                'idea_id' => 9,
            ],
            [
                'desc' => 'Marketing would definitely benefit from these AI-driven insights.',
                'is_anonymous' => false,
                'user_id' => 5, // alex_thompson
                'idea_id' => 9,
            ],
            [
                'desc' => 'This aligns with our ESG goals. Great proposal!',
                'is_anonymous' => false,
                'user_id' => 2, // sarah_wilson
                'idea_id' => 10,
            ],
            [
                'desc' => 'The ROI on wellness programs has been proven in multiple studies. This is a great investment.',
                'is_anonymous' => false,
                'user_id' => 10, // jennifer_williams
                'idea_id' => 11,
            ],
            [
                'desc' => 'We should ensure this includes support for remote workers as well.',
                'is_anonymous' => false,
                'user_id' => 5, // alex_thompson
                'idea_id' => 11,
            ],
            [
                'desc' => 'This would significantly reduce our paper usage. Fully support!',
                'is_anonymous' => false,
                'user_id' => 7, // david_lee
                'idea_id' => 12,
            ],
            [
                'desc' => 'Security should definitely be our top priority given recent breaches in the industry.',
                'is_anonymous' => false,
                'user_id' => 2, // sarah_wilson
                'idea_id' => 13,
            ],
            [
                'desc' => 'Could we include phishing simulation exercises as part of the training?',
                'is_anonymous' => false,
                'user_id' => 11, // daniel_brown
                'idea_id' => 13,
            ],
            [
                'desc' => 'I volunteer with a local school and they would greatly appreciate corporate mentorship.',
                'is_anonymous' => false,
                'user_id' => 12, // michelle_garcia
                'idea_id' => 14,
            ],
            [
                'desc' => 'Our current knowledge base is difficult to navigate. This would be very helpful.',
                'is_anonymous' => false,
                'user_id' => 13, // james_miller
                'idea_id' => 15,
            ],
            [
                'desc' => 'Would this include video tutorials? Those are often more effective than text.',
                'is_anonymous' => true,
                'user_id' => 4, // emma_davis
                'idea_id' => 15,
            ],
        ];

        foreach ($comments as $comment) {
            Comment::create($comment);
        }

        // Create Reactions
        $reactions = [
            // Idea 1: Automated Testing Framework (45 views)
            [
                'user_id' => 2, // sarah_wilson
                'idea_id' => 1,
                'reaction' => 'like',
                'remark' => 'Supporting the automated testing initiative',
            ],
            [
                'user_id' => 9, // robert_johnson
                'idea_id' => 1,
                'reaction' => 'like',
                'remark' => 'This will improve our software quality',
            ],
            [
                'user_id' => 11, // daniel_brown
                'idea_id' => 1,
                'reaction' => 'like',
                'remark' => 'Will reduce manual testing time',
            ],
            [
                'user_id' => 5, // alex_thompson
                'idea_id' => 1,
                'reaction' => 'like',
                'remark' => 'Great initiative, will save us time',
            ],
            [
                'user_id' => 13, // james_miller
                'idea_id' => 1,
                'reaction' => 'like',
                'remark' => 'Much needed improvement',
            ],
            [
                'user_id' => 7, // david_lee
                'idea_id' => 1,
                'reaction' => 'unlike',
                'remark' => 'Seems too complex for our current needs',
            ],
            
            // Idea 2: Improve Student Onboarding Process (38 views)
            [
                'user_id' => 3, // michael_chen
                'idea_id' => 2,
                'reaction' => 'like',
                'remark' => 'Agree with the onboarding improvements',
            ],
            [
                'user_id' => 4, // emma_davis
                'idea_id' => 2,
                'reaction' => 'unlike',
                'remark' => 'Current process works fine',
            ],
            [
                'user_id' => 12, // michelle_garcia
                'idea_id' => 2,
                'reaction' => 'like',
                'remark' => 'Would streamline HR processes',
            ],
            [
                'user_id' => 6, // lisa_park
                'idea_id' => 2,
                'reaction' => 'like',
                'remark' => 'This would help new employees',
            ],
            [
                'user_id' => 9, // robert_johnson
                'idea_id' => 2,
                'reaction' => 'like',
                'remark' => 'Good for building team culture',
            ],
            
            // Idea 3: Enhance Student Support System (52 views)
            [
                'user_id' => 1, // john_doe
                'idea_id' => 3,
                'reaction' => 'like',
                'remark' => 'Student support needs this upgrade',
            ],
            [
                'user_id' => 2, // sarah_wilson
                'idea_id' => 3,
                'reaction' => 'like',
                'remark' => 'Will improve student satisfaction',
            ],
            [
                'user_id' => 5, // alex_thompson
                'idea_id' => 3,
                'reaction' => 'like',
                'remark' => 'Essential for growth',
            ],
            [
                'user_id' => 8, // sophia_rodriguez
                'idea_id' => 3,
                'reaction' => 'like',
                'remark' => 'The AI features sound promising',
            ],
            [
                'user_id' => 10, // jennifer_williams
                'idea_id' => 3,
                'reaction' => 'unlike',
                'remark' => 'Concerns about data privacy',
            ],
            [
                'user_id' => 11, // daniel_brown
                'idea_id' => 3,
                'reaction' => 'like',
                'remark' => 'Would save support team time',
            ],
            [
                'user_id' => 13, // james_miller
                'idea_id' => 3,
                'reaction' => 'like',
                'remark' => 'As a support team member, this would help',
            ],
            
            // Idea 4: Green Campus Initiative (42 views) 
            [
                'user_id' => 5, // alex_thompson
                'idea_id' => 4,
                'reaction' => 'like',
                'remark' => 'Great initiative for sustainability',
            ],
            [
                'user_id' => 3, // michael_chen
                'idea_id' => 4,
                'reaction' => 'like',
                'remark' => 'Appreciate the environmental focus',
            ],
            [
                'user_id' => 9, // robert_johnson
                'idea_id' => 4,
                'reaction' => 'like',
                'remark' => 'Will reduce our carbon footprint',
            ],
            [
                'user_id' => 1, // john_doe
                'idea_id' => 4,
                'reaction' => 'like',
                'remark' => 'Aligns with our corporate values',
            ],
            [
                'user_id' => 12, // michelle_garcia
                'idea_id' => 4,
                'reaction' => 'unlike',
                'remark' => 'Implementation might be challenging',
            ],
            
            // Idea 5: Faculty Skill Development Program (35 views)
            [
                'user_id' => 6, // lisa_park
                'idea_id' => 5,
                'reaction' => 'like',
                'remark' => 'This would be very beneficial for all employees',
            ],
            [
                'user_id' => 8, // sophia_rodriguez
                'idea_id' => 5,
                'reaction' => 'like',
                'remark' => 'Skills development is crucial',
            ],
            [
                'user_id' => 2, // sarah_wilson
                'idea_id' => 5,
                'reaction' => 'like',
                'remark' => 'Great for employee retention',
            ],
            [
                'user_id' => 10, // jennifer_williams
                'idea_id' => 5,
                'reaction' => 'like',
                'remark' => 'Would boost productivity',
            ],
            
            // Idea 6: Cross-Departmental Collaboration Platform (29 views)
            [
                'user_id' => 3, // michael_chen
                'idea_id' => 6,
                'reaction' => 'like',
                'remark' => 'Will improve cross-team cooperation',
            ],
            [
                'user_id' => 9, // robert_johnson
                'idea_id' => 6,
                'reaction' => 'like',
                'remark' => 'Helps break down silos',
            ],
            [
                'user_id' => 4, // emma_davis
                'idea_id' => 6,
                'reaction' => 'unlike',
                'remark' => 'We already have too many platforms',
            ],
            
            // Idea 7: Implement Flexible Learning Arrangements (67 views - highest!)
            [
                'user_id' => 7, // david_lee
                'idea_id' => 7,
                'reaction' => 'like',
                'remark' => 'Work flexibility is important',
            ],
            [
                'user_id' => 5, // alex_thompson
                'idea_id' => 7,
                'reaction' => 'like',
                'remark' => 'This could boost student satisfaction',
            ],
            [
                'user_id' => 1, // john_doe
                'idea_id' => 7,
                'reaction' => 'like',
                'remark' => 'Important for work-life balance',
            ],
            [
                'user_id' => 2, // sarah_wilson
                'idea_id' => 7,
                'reaction' => 'like',
                'remark' => 'Essential for modern workplace',
            ],
            [
                'user_id' => 4, // emma_davis
                'idea_id' => 7,
                'reaction' => 'like',
                'remark' => 'Would improve morale',
            ],
            [
                'user_id' => 6, // lisa_park
                'idea_id' => 7,
                'reaction' => 'like',
                'remark' => 'Great for working parents',
            ],
            [
                'user_id' => 10, // jennifer_williams
                'idea_id' => 7,
                'reaction' => 'like',
                'remark' => 'Can increase productivity',
            ],
            [
                'user_id' => 9, // robert_johnson
                'idea_id' => 7,
                'reaction' => 'unlike',
                'remark' => 'Concerned about coordination issues',
            ],
            [
                'user_id' => 11, // daniel_brown
                'idea_id' => 7,
                'reaction' => 'like',
                'remark' => 'Great for team morale',
            ],
            
            // Idea 8: Student Feedback Analysis System (41 views) 
            [
                'user_id' => 2, // sarah_wilson
                'idea_id' => 8,
                'reaction' => 'like',
                'remark' => 'Student feedback is valuable',
            ],
            [
                'user_id' => 1, // john_doe
                'idea_id' => 8,
                'reaction' => 'like',
                'remark' => 'Will help product development',
            ],
            [
                'user_id' => 7, // david_lee
                'idea_id' => 8,
                'reaction' => 'like',
                'remark' => 'Good for iterative improvements',
            ],
            [
                'user_id' => 13, // james_miller
                'idea_id' => 8,
                'reaction' => 'like',
                'remark' => 'As a support member, this is needed',
            ],
            [
                'user_id' => 3, // michael_chen
                'idea_id' => 8,
                'reaction' => 'unlike',
                'remark' => 'Implementation could be complex',
            ],
            
            // Idea 9: AI-Powered Research Trend Analysis (56 views)
            [
                'user_id' => 9, // robert_johnson
                'idea_id' => 9,
                'reaction' => 'like',
                'remark' => 'AI can provide valuable insights',
            ],
            [
                'user_id' => 1, // john_doe
                'idea_id' => 9,
                'reaction' => 'like',
                'remark' => 'Strategic advantage for our company',
            ],
            [
                'user_id' => 2, // sarah_wilson
                'idea_id' => 9,
                'reaction' => 'like',
                'remark' => 'The market analysis will be useful',
            ],
            [
                'user_id' => 6, // lisa_park
                'idea_id' => 9,
                'reaction' => 'like',
                'remark' => 'Could revolutionize our approach',
            ],
            [
                'user_id' => 8, // sophia_rodriguez
                'idea_id' => 9,
                'reaction' => 'like',
                'remark' => 'This is the future of marketing',
            ],
            [
                'user_id' => 3, // michael_chen
                'idea_id' => 9,
                'reaction' => 'like',
                'remark' => 'Technology-forward thinking',
            ],
            [
                'user_id' => 7, // david_lee
                'idea_id' => 9,
                'reaction' => 'unlike',
                'remark' => 'Concerned about accuracy of AI',
            ],
            
            // Idea 10: Sustainable Campus Resource Management (38 views)
            [
                'user_id' => 4, // emma_davis
                'idea_id' => 10,
                'reaction' => 'like',
                'remark' => 'Sustainability should be a priority',
            ],
            [
                'user_id' => 3, // michael_chen
                'idea_id' => 10,
                'reaction' => 'like',
                'remark' => 'Great for our company image',
            ],
            [
                'user_id' => 12, // michelle_garcia
                'idea_id' => 10,
                'reaction' => 'like',
                'remark' => 'Ethical considerations are important',
            ],
            [
                'user_id' => 10, // jennifer_williams
                'idea_id' => 10,
                'reaction' => 'unlike',
                'remark' => 'May increase costs significantly',
            ],
            
            // Idea 11: Wellness Program for Students and Faculty (61 views)
            [
                'user_id' => 1, // john_doe
                'idea_id' => 11,
                'reaction' => 'like',
                'remark' => 'Student wellness is important',
            ],
            [
                'user_id' => 4, // emma_davis
                'idea_id' => 11,
                'reaction' => 'like',
                'remark' => 'Great for overall company health',
            ],
            [
                'user_id' => 6, // lisa_park
                'idea_id' => 11,
                'reaction' => 'like',
                'remark' => 'Mental health focus is appreciated',
            ],
            [
                'user_id' => 9, // robert_johnson
                'idea_id' => 11,
                'reaction' => 'like',
                'remark' => 'Reduces absenteeism',
            ],
            [
                'user_id' => 12, // michelle_garcia
                'idea_id' => 11,
                'reaction' => 'like',
                'remark' => 'Great for team building',
            ],
            [
                'user_id' => 2, // sarah_wilson
                'idea_id' => 11,
                'reaction' => 'like',
                'remark' => 'Well-rounded approach',
            ],
            [
                'user_id' => 7, // david_lee
                'idea_id' => 11,
                'reaction' => 'unlike',
                'remark' => 'Participation might be low',
            ],
            
            // Idea 12: Digital Documentation System (32 views)
            [
                'user_id' => 13, // james_miller
                'idea_id' => 12,
                'reaction' => 'like',
                'remark' => 'Will reduce paper waste',
            ],
            [
                'user_id' => 2, // sarah_wilson
                'idea_id' => 12,
                'reaction' => 'like',
                'remark' => 'Much more efficient',
            ],
            [
                'user_id' => 5, // alex_thompson
                'idea_id' => 12,
                'reaction' => 'like',
                'remark' => 'Improves searchability',
            ],
            
            // Idea 13: Advanced Cybersecurity Training Program (48 views)
            [
                'user_id' => 10, // jennifer_williams
                'idea_id' => 13,
                'reaction' => 'like',
                'remark' => 'Security training is essential',
            ],
            [
                'user_id' => 3, // michael_chen
                'idea_id' => 13,
                'reaction' => 'like',
                'remark' => 'Critical for data protection',
            ],
            [
                'user_id' => 5, // alex_thompson
                'idea_id' => 13,
                'reaction' => 'like',
                'remark' => 'Recent incidents make this important',
            ],
            [
                'user_id' => 6, // lisa_park
                'idea_id' => 13,
                'reaction' => 'like',
                'remark' => 'Should be mandatory for all staff',
            ],
            [
                'user_id' => 12, // michelle_garcia
                'idea_id' => 13,
                'reaction' => 'like',
                'remark' => 'This affects everyone',
            ],
            [
                'user_id' => 9, // robert_johnson
                'idea_id' => 13,
                'reaction' => 'unlike',
                'remark' => 'Too technical for some employees',
            ],
            
            // Idea 14: Community Engagement Initiative (37 views)
            [
                'user_id' => 11, // daniel_brown
                'idea_id' => 14,
                'reaction' => 'unlike',
                'remark' => 'Not sure about resource allocation',
            ],
            [
                'user_id' => 5, // alex_thompson
                'idea_id' => 14,
                'reaction' => 'like',
                'remark' => 'Great for public relations',
            ],
            [
                'user_id' => 2, // sarah_wilson
                'idea_id' => 14,
                'reaction' => 'like',
                'remark' => 'Builds positive company image',
            ],
            [
                'user_id' => 4, // emma_davis
                'idea_id' => 14,
                'reaction' => 'like',
                'remark' => 'Community support is important',
            ],
            
            // Idea 15: Academic Knowledge Base Enhancement (27 views)
            [
                'user_id' => 8, // sophia_rodriguez
                'idea_id' => 15,
                'reaction' => 'like',
                'remark' => 'Knowledge sharing is important',
            ],
            [
                'user_id' => 1, // john_doe
                'idea_id' => 15,
                'reaction' => 'like',
                'remark' => 'Would reduce repeat questions',
            ],
            [
                'user_id' => 7, // david_lee
                'idea_id' => 15,
                'reaction' => 'like',
                'remark' => 'Current system needs updating',
            ],
        ];

        foreach ($reactions as $reaction) {
            Reaction::create($reaction);
        }

        // Create Reported Ideas
        $reportedIdeas = [
            [
                'idea_id' => 3,
                'user_id' => 4, // emma_davis
            ],
            [
                'idea_id' => 9,
                'user_id' => 11, // daniel_brown
            ],
        ];

        foreach ($reportedIdeas as $reportedIdea) {
            ReportedIdea::create($reportedIdea);
        }

        // Setup demo files
        $this->setupDemoFiles();

        // Create Idea Documents with actual files
        $ideaDocuments = [
            [
                'idea_id' => 1,
                'file_name' => 'Automated Testing Framework Proposal',
                'public_file_url' => '/storage/idea_documents/file-sample_150kB.pdf',
                'remark' => 'Detailed technical proposal with implementation plan',
            ],
            [
                'idea_id' => 2,
                'file_name' => 'Digital Onboarding Process Design',
                'public_file_url' => '/storage/idea_documents/university-5188610_1280.jpg',
                'remark' => 'Process improvement proposal with workflow diagrams',
            ],
            [
                'idea_id' => 3,
                'file_name' => 'Customer Support System Architecture',
                'public_file_url' => '/storage/idea_documents/library-1400313_1280.jpg',
                'remark' => 'Technical architecture for new support system',
            ],
            [
                'idea_id' => 4,
                'file_name' => 'Green Office Initiative Plan',
                'public_file_url' => '/storage/idea_documents/college-75535_1280.jpg',
                'remark' => 'Sustainability proposal with cost analysis',
            ],
            [
                'idea_id' => 5,
                'file_name' => 'Employee Development Program Outline',
                'public_file_url' => '/storage/idea_documents/file-sample_150kB.pdf',
                'remark' => 'Comprehensive training program outline',
            ],
            [
                'idea_id' => 7,
                'file_name' => 'Flexible Work Policy Draft',
                'public_file_url' => '/storage/idea_documents/file-sample_150kB.pdf',
                'remark' => 'Policy document with implementation guidelines',
            ],
            [
                'idea_id' => 9,
                'file_name' => 'AI Market Analysis Whitepaper',
                'public_file_url' => '/storage/idea_documents/file-sample_150kB.pdf',
                'remark' => 'Technical whitepaper on market analysis AI',
            ],
            [
                'idea_id' => 11,
                'file_name' => 'Employee Wellness Program Presentation',
                'public_file_url' => '/storage/idea_documents/university-5188610_1280.jpg',
                'remark' => 'Program presentation with budget estimates',
            ],
            [
                'idea_id' => 13,
                'file_name' => 'Security Training Curriculum',
                'public_file_url' => '/storage/idea_documents/file-sample_150kB.pdf',
                'remark' => 'Comprehensive curriculum for security training',
            ],
        ];

        foreach ($ideaDocuments as $document) {
            IdeaDocument::create($document);
        }

        $this->runArtisanStorageLink();
    }

    /**
     * Setup demo files by copying them from demo_files to storage/app/public/idea_documents
     */
    private function setupDemoFiles()
    {
        // Ensure the target directory exists
        Storage::disk('public')->makeDirectory('idea_documents', 0755, true);
        
        // Get all files from the demo_files directory
        $demoFilesPath = database_path('seeders/demo_files');
        $files = File::files($demoFilesPath);

        // Copy each file to the storage directory
        foreach ($files as $file) {
            $filename = $file->getFilename();
            $sourcePath = $demoFilesPath . '/' . $filename;
            $destinationPath = 'idea_documents/' . $filename;
            
            if (File::exists($sourcePath)) {
                // Copy file to storage/app/public/idea_documents
                Storage::disk('public')->put($destinationPath, File::get($sourcePath));
            }
        }
        
        $this->command->info('Demo files copied to storage successfully!');
    }
    

    private function runArtisanStorageLink()
    {
        $this->command->info('Running Artisan storage:link...');
        Artisan::call('storage:link');
        $this->command->info('Artisan storage:link completed successfully!');
    }

} 