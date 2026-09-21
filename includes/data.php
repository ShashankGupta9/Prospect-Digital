<?php
/**
 * Prospect Digital — website content
 * ---------------------------------------------------------------------------
 * All page copy lives here so that editing words never means editing layout.
 * Every service and product page is generated from these arrays, which keeps
 * the site consistent and makes it easy to add a 9th service later:
 *   1. add an entry below,   2. copy a file in /services/ or /products/,
 *   3. change the slug inside it.
 *
 * NOTE: service & product copy is business content supplied for Prospect
 * Digital. Replace the illustrative engagement snapshots with real case
 * studies as soon as client permissions are available.
 * ---------------------------------------------------------------------------
 */

/* =========================================================================
   NAVIGATION
   ========================================================================= */
$PD_NAV = [
    ['label' => 'Services', 'path' => 'services', 'folder' => true],
    ['label' => 'Products', 'path' => 'products', 'folder' => true],
    ['label' => 'Work',     'path' => 'projects', 'folder' => false],
    ['label' => 'Store',    'path' => 'store',    'folder' => false],
    ['label' => 'About',    'path' => 'about',    'folder' => false],
    ['label' => 'Contact',  'path' => 'contact',  'folder' => false],
];

/* =========================================================================
   SERVICES  (8)
   ========================================================================= */
$PD_SERVICES = [

    /* ------------------------------------------------------------------ 01 */
    'software-development' => [
        'number'            => '01',
        'slug'              => 'software-development',
        'img'               => 'images/services/software-development.jpg',
        'name'              => 'Software Development',
        'nav'               => 'Development',
        'eyebrow'           => 'SOFTWARE / 01 OF 08',
        'breadcrumb'        => 'Home / Services / Development',
        'card_tag'          => 'Custom ERP, CRM, billing and operations software built around your workflow.',
        'icon'              => 'code',
        'visual'            => 'table',
        'hero_title'        => 'Software that runs your business, not the other way round.',
        'hero_description'  => 'Custom ERP, CRM, billing, inventory and operations software for growing businesses in Bhopal and across India.',
        'quick_answer'      => 'Custom software development means building the business tool you need — ERP, CRM, billing or operations software — instead of adapting to packaged apps. We design, build and support it end-to-end, so businesses in Bhopal and across India get software shaped around their workflow.',
        'outcome_label'     => 'THE OUTCOME',
        'outcome_title'     => 'Design custom software around the way your business actually works.',
        'outcome_description' => 'We build ERP, CRM, billing, inventory, portals, dashboards, integrations and mobile apps — connecting sales, customers, stock, staff and reporting in one system.',
        'deliverables'      => [
            'ERP, CRM and operations software',
            'Billing, invoicing and inventory modules',
            'Customer and admin portals',
            'Dashboards and management reports',
            'Third-party integrations and APIs',
            'Mobile apps for Android and iOS',
        ],
        'without' => [
            'title'  => 'Processes run on files, chats and memory.',
            'points' => [
                'Records live in different places — searching takes time.',
                'Billing, stock and follow-ups depend on manual work.',
                'Reporting is difficult when the business needs answers.',
                'Errors and duplicate work grow with the business.',
            ],
        ],
        'with' => [
            'title'  => 'One system runs the operations.',
            'points' => [
                'Customers, sales, stock and billing live in one searchable place.',
                'Reports and dashboards show what the business needs to decide.',
                'Team access and roles keep data safe and clear.',
                'Workflows match the business — not the other way round.',
            ],
        ],
        'process' => [
            ['number' => '01', 'title' => 'Understand your business', 'description' => 'We explain, confirm and keep you updated at every stage.'],
            ['number' => '02', 'title' => 'Plan the system',          'description' => 'Clear responsibilities and progress updates throughout.'],
            ['number' => '03', 'title' => 'Build in clear stages',    'description' => 'Built, tested and reviewed with you before launch.'],
            ['number' => '04', 'title' => 'Launch, train and support', 'description' => 'Support and improvements continue after the launch.'],
        ],
        'faqs' => [
            ['q' => 'How long does custom software take to build?', 'a' => 'A focused module such as billing or a customer portal usually takes 4–8 weeks. A full ERP covering sales, stock, billing and reporting typically runs 3–6 months, delivered in stages so you start using value early.'],
            ['q' => 'Can it connect to software we already use?', 'a' => 'Yes. We integrate with existing tools — Tally, payment gateways, WhatsApp, e-mail, SMS, GST/e-invoice utilities and third-party APIs — so you are not forced to abandon working systems.'],
            ['q' => 'Do we own the software and the source code?', 'a' => 'Yes. You own the software built for you, along with the database and source code, and we hand over documentation at the end of the project.'],
        ],
        'cta_label'         => 'READY TO BUILD?',
        'cta_title'         => "Let's build your system.",
        'cta_description'   => 'Share your requirement and we will map the right software scope, timeline and cost.',
        'meta_title'        => 'Software Development Company in Bhopal | Custom ERP, CRM & Billing Software',
        'meta_description'  => 'Custom software development in Bhopal — ERP, CRM, billing, inventory, portals, dashboards and mobile apps built around your workflow. Get a free consultation.',
    ],

    /* ------------------------------------------------------------------ 02 */
    'website-development' => [
        'number'            => '02',
        'slug'              => 'website-development',
        'img'               => 'images/services/website-development.jpg',
        'name'              => 'Website Development',
        'nav'               => 'Websites',
        'eyebrow'           => 'WEBSITES / 02 OF 08',
        'breadcrumb'        => 'Home / Services / Websites',
        'card_tag'          => 'Fast, mobile-first websites and stores that earn trust and generate enquiries.',
        'icon'              => 'globe',
        'visual'            => 'website',
        'hero_title'        => 'Websites that earn trust before the first call.',
        'hero_description'  => 'Corporate websites, landing pages and online stores built for speed, search visibility and enquiries — not just good looks.',
        'quick_answer'      => 'Website development is the work of designing, building and launching a site that represents your business online: structure, design, content, speed, SEO basics and analytics. We build corporate websites, campaign landing pages, catalogues and e-commerce stores that load fast, work on every device and are easy for your team to keep updated.',
        'outcome_label'     => 'THE OUTCOME',
        'outcome_title'     => 'A website built to be found, trusted and acted upon.',
        'outcome_description' => 'Every page is planned around a business goal — an enquiry, a booking, an order or credibility — then built with clean code, real performance budgets and analytics you can actually read.',
        'deliverables'      => [
            'Corporate and multi-page business websites',
            'Campaign and launch landing pages',
            'E-commerce and product catalogue storefronts',
            'Content editing your team can manage safely',
            'Technical SEO, schema markup and sitemaps',
            'Speed, Core Web Vitals and accessibility fixes',
        ],
        'without' => [
            'title'  => 'The website is a digital brochure nobody visits.',
            'points' => [
                'Pages load slowly on mobile networks and lose visitors.',
                'Enquiry and contact paths are unclear or hidden.',
                'Content cannot be updated without calling a developer.',
                'No analytics, so marketing decisions are guesses.',
            ],
        ],
        'with' => [
            'title'  => 'The website becomes your best salesperson.',
            'points' => [
                'Fast, mobile-first pages designed for how people actually browse.',
                'Clear calls to action — enquiry form, call, WhatsApp, directions.',
                'Search-ready structure with titles, schema and sitemaps done right.',
                'Tracking in place, so you can see which pages bring enquiries.',
            ],
        ],
        'process' => [
            ['number' => '01', 'title' => 'Plan the pages',       'description' => 'We map every page to a purpose and a call to action.'],
            ['number' => '02', 'title' => 'Design the direction',  'description' => 'Layout and visual direction approved before code.'],
            ['number' => '03', 'title' => 'Build and test',        'description' => 'Responsive build, tested on real phones and browsers.'],
            ['number' => '04', 'title' => 'Launch and measure',    'description' => 'Go live, then improve using real visitor data.'],
        ],
        'faqs' => [
            ['q' => 'How long does a business website take?', 'a' => 'A 5–8 page corporate website typically takes 3–5 weeks from content approval to launch. Larger catalogues or e-commerce stores usually run 6–10 weeks depending on the number of products and integrations.'],
            ['q' => 'Will we be able to update content ourselves?', 'a' => 'Yes. We build an editing layer for the sections you change often — services, products, offers, testimonials, blog posts — and train your team on it during handover.'],
            ['q' => 'Can you redesign an existing site without losing rankings?', 'a' => 'We audit current URLs, keywords and backlinks first, then map old pages to new ones and set up redirects. Content and technical SEO are preserved deliberately rather than rebuilt from scratch.'],
        ],
        'cta_label'         => 'START YOUR WEBSITE',
        'cta_title'         => 'Let us plan your next website.',
        'cta_description'   => 'Tell us what the site should achieve and we will suggest structure, timeline and cost.',
        'meta_title'        => 'Website Development Company in Bhopal | Fast, SEO-Ready Websites',
        'meta_description'  => 'Website development in Bhopal — corporate websites, landing pages and e-commerce stores built for speed, search visibility and enquiries. Talk to Prospect Digital.',
    ],

    /* ------------------------------------------------------------------ 03 */
    'digital-marketing' => [
        'number'            => '03',
        'slug'              => 'digital-marketing',
        'img'               => 'images/services/digital-marketing.jpg',
        'name'              => 'Digital Marketing',
        'nav'               => 'Marketing',
        'eyebrow'           => 'MARKETING / 03 OF 08',
        'breadcrumb'        => 'Home / Services / Marketing',
        'card_tag'          => 'SEO, content, social and e-mail programs tied to enquiries and revenue.',
        'icon'              => 'megaphone',
        'visual'            => 'analytics',
        'hero_title'        => 'Marketing that is measured, not guessed.',
        'hero_description'  => 'SEO, content, social and e-mail programs for growing Indian businesses — tracked from the first click to the closed deal.',
        'quick_answer'      => 'Digital marketing is the ongoing work of getting your business in front of the right people online and turning that attention into enquiries. It covers search engine optimisation, content, social media, e-mail and local visibility — measured with analytics so effort can be repeated where it works.',
        'outcome_label'     => 'THE OUTCOME',
        'outcome_title'     => 'A steady flow of enquiries you can trace back to the work.',
        'outcome_description' => 'We build a channel mix around your buyers, publish consistently, and report on the numbers that matter: impressions, enquiries, cost per enquiry and closed business.',
        'deliverables'      => [
            'Technical and on-page SEO',
            'Content planning, writing and publishing',
            'Google Business Profile and local SEO',
            'Social media management and creative',
            'E-mail and WhatsApp campaign flows',
            'Monthly reporting tied to enquiries and spend',
        ],
        'without' => [
            'title'  => 'Effort is spread thin across channels.',
            'points' => [
                'Posts and campaigns happen without a plan or calendar.',
                'Nobody can say which channel brings real enquiries.',
                'Website and listings are not found for what you sell.',
                'Reporting is a screenshot album, not a decision tool.',
            ],
        ],
        'with' => [
            'title'  => 'Channels work together with clear numbers.',
            'points' => [
                'A documented strategy with owners, calendars and budgets.',
                'Rankings and local visibility built page by page.',
                'Enquiries tagged so you know the source of every lead.',
                'Monthly reviews that reallocate effort to what performs.',
            ],
        ],
        'process' => [
            ['number' => '01', 'title' => 'Audit and benchmark',   'description' => 'Where you stand today, competitors included.'],
            ['number' => '02', 'title' => 'Strategy and channel mix', 'description' => 'What we will do, in what order, and why.'],
            ['number' => '03', 'title' => 'Execute and publish',   'description' => 'Content, campaigns and listings shipped on schedule.'],
            ['number' => '04', 'title' => 'Measure and scale',     'description' => 'Monthly reporting, then double down on winners.'],
        ],
        'faqs' => [
            ['q' => 'How soon do results appear?', 'a' => 'Paid and local visibility can move within weeks. Organic SEO generally shows meaningful movement in 3–6 months because rankings build on content, links and site health over time.'],
            ['q' => 'Do you work with small businesses?', 'a' => 'Yes. Many clients start with a single channel — usually local SEO or one paid platform — prove the numbers, and expand from there.'],
            ['q' => 'How is success reported?', 'a' => 'A monthly report covering traffic, ranking movement, enquiries by source, cost per enquiry and what we plan to change next month.'],
        ],
        'cta_label'         => 'GROW YOUR REACH',
        'cta_title'         => 'Build a marketing engine, not a to-do list.',
        'cta_description'   => 'Tell us your market and goals — we will propose a channel plan with a realistic timeline.',
        'meta_title'        => 'Digital Marketing Agency in Bhopal | SEO, Content & Social Media',
        'meta_description'  => 'Digital marketing services in Bhopal — SEO, content, social media, e-mail and local search programs measured on enquiries, not vanity metrics.',
    ],

    /* ------------------------------------------------------------------ 04 */
    'performance-marketing' => [
        'number'            => '04',
        'slug'              => 'performance-marketing',
        'img'               => 'images/services/performance-marketing.jpg',
        'name'              => 'Paid Ads',
        'nav'               => 'Paid Ads',
        'eyebrow'           => 'PAID ADS / 04 OF 08',
        'breadcrumb'        => 'Home / Services / Paid Ads',
        'card_tag'          => 'Google, Meta and LinkedIn campaigns judged on lead quality and returns.',
        'icon'              => 'target',
        'visual'            => 'funnel',
        'hero_title'        => 'Every rupee of ad spend should earn its place.',
        'hero_description'  => 'Google, Meta and LinkedIn campaigns built around lead quality, cost per enquiry and returns — not impressions.',
        'quick_answer'      => 'Paid ads — also called performance marketing — means buying visibility on search engines and social platforms, then optimising until the cost of acquiring a customer makes business sense. We build the campaign, the creative, the landing page and the tracking together, because results come from that whole chain.',
        'outcome_label'     => 'THE OUTCOME',
        'outcome_title'     => 'Predictable enquiries at a cost you can defend.',
        'outcome_description' => 'We start from unit economics — what a customer is worth to you — then design campaigns, offers and landing pages that reach that number and stay there.',
        'deliverables'      => [
            'Google Search, Performance Max and YouTube campaigns',
            'Meta (Facebook and Instagram) lead and sales campaigns',
            'LinkedIn campaigns for B2B pipeline',
            'Retargeting, audience building and lookalikes',
            'Landing pages and creative built per offer',
            'Conversion tracking, dashboards and weekly optimisation',
        ],
        'without' => [
            'title'  => 'Spend goes out without clear return.',
            'points' => [
                'Campaigns run on default settings and broad audiences.',
                'One creative is reused until it stops performing.',
                'Enquiries arrive with no source data or tracking.',
                'Nobody cuts the keywords and audiences that waste money.',
            ],
        ],
        'with' => [
            'title'  => 'Spend is managed like an investment.',
            'points' => [
                'Tracking in place before the first rupee is spent.',
                'Offers, audiences and creatives tested systematically.',
                'Wasteful keywords and placements removed weekly.',
                'Cost per enquiry and per customer reported honestly.',
            ],
        ],
        'process' => [
            ['number' => '01', 'title' => 'Audit and tracking setup', 'description' => 'Accounts, pixels and conversions verified first.'],
            ['number' => '02', 'title' => 'Offer, audience, creative',  'description' => 'What we say, to whom, and in what form.'],
            ['number' => '03', 'title' => 'Launch, test, cut waste',    'description' => 'Structured tests with clear pass/fail rules.'],
            ['number' => '04', 'title' => 'Scale what works',           'description' => 'Increase budget only where returns hold.'],
        ],
        'faqs' => [
            ['q' => 'How much budget do we need to start?', 'a' => 'Most B2B and local campaigns need at least ₹30,000–₹50,000 per month in media spend to gather enough data to optimise. This is separate from the management fee and is paid directly to Google or Meta.'],
            ['q' => 'Do you guarantee a number of leads?', 'a' => 'No honest agency can guarantee exact lead volumes. We agree on a target cost per enquiry, work towards it with weekly optimisation, and report the true numbers — including months when performance dips.'],
            ['q' => 'Who owns the ad accounts?', 'a' => 'You do. Accounts, pixels and audiences are created under your ownership, so nothing is ever held hostage if we stop working together.'],
        ],
        'cta_label'         => 'PLAN YOUR CAMPAIGNS',
        'cta_title'         => 'Let us find your cost per enquiry.',
        'cta_description'   => 'Share your offer and target market — we will outline a campaign structure and realistic budget.',
        'meta_title'        => 'Paid Ads Management in Bhopal | Google & Meta Performance Marketing',
        'meta_description'  => 'Performance marketing services in Bhopal — Google Ads, Meta ads and LinkedIn campaigns optimised for lead quality, cost per enquiry and returns.',
    ],

    /* ------------------------------------------------------------------ 05 */
    'branding-creative' => [
        'number'            => '05',
        'slug'              => 'branding-creative',
        'img'               => 'images/services/branding-creative.jpg',
        'name'              => 'Branding & Creative',
        'nav'               => 'Branding',
        'eyebrow'           => 'BRANDING / 05 OF 08',
        'breadcrumb'        => 'Home / Services / Branding',
        'card_tag'          => 'Identity, messaging and design systems that make you look as good as you are.',
        'icon'              => 'palette',
        'visual'            => 'brand',
        'hero_title'        => 'A brand people recognise and remember.',
        'hero_description'  => 'Identity, messaging and design systems that make a growing business look as credible as it truly is.',
        'quick_answer'      => 'Branding is the deliberate design of how a business looks, sounds and is remembered — its logo and identity, colour and typography, messaging, and the templates the whole team uses. Creative work turns that system into the materials you need every month: decks, brochures, packaging, social posts and campaign artwork.',
        'outcome_label'     => 'THE OUTCOME',
        'outcome_title'     => 'One consistent identity across every touchpoint.',
        'outcome_description' => 'Whether a customer meets you on a banner, a billboard, a brochure or a landing page, they see the same confident brand — and your team stops rebuilding files from scratch.',
        'deliverables'      => [
            'Logo and complete visual identity system',
            'Brand guidelines, colour and typography',
            'Positioning, messaging and taglines',
            'Stationery, decks and company profile documents',
            'Product, UI and packaging design',
            'Social, print and campaign creative',
        ],
        'without' => [
            'title'  => 'Every file is designed from scratch.',
            'points' => [
                'Logo, colours and fonts change with each vendor.',
                'Messaging differs on every brochure and profile.',
                'Documents and decks look dated next to competitors.',
                'Design work is slow because nothing is standardised.',
            ],
        ],
        'with' => [
            'title'  => 'A system that keeps everything consistent.',
            'points' => [
                'A documented identity with rules, not opinions.',
                'Templates your team can reuse in minutes.',
                'Positioning and copy that sound like one company.',
                'Materials that hold up in front of large buyers.',
            ],
        ],
        'process' => [
            ['number' => '01', 'title' => 'Discover the brand', 'description' => 'Audience, competitors and what you truly stand for.'],
            ['number' => '02', 'title' => 'Position and message', 'description' => 'The words and promise before the visuals.'],
            ['number' => '03', 'title' => 'Design the identity',  'description' => 'Logo, system, guidelines and templates.'],
            ['number' => '04', 'title' => 'Roll out the brand',  'description' => 'Websites, decks, socials and print in one voice.'],
        ],
        'faqs' => [
            ['q' => 'Can you refresh our logo without starting over?', 'a' => 'Yes. Many clients already have recognition worth protecting, so we evolve the existing mark and rebuild the system around it instead of discarding equity.'],
            ['q' => 'What do we receive at the end?', 'a' => 'Logo files in all common formats (SVG, PNG, PDF), a brand guideline document, colour and type specifications, and editable templates for the materials your team uses most.'],
            ['q' => 'Do you handle printing and production?', 'a' => 'We prepare print-ready files and coordinate with your printer or vendor, including material and finish recommendations, so what you approve is what gets produced.'],
        ],
        'cta_label'         => 'SHARPEN YOUR BRAND',
        'cta_title'         => 'Let us give your brand a system.',
        'cta_description'   => 'Share where your brand feels inconsistent and we will propose a scope.',
        'meta_title'        => 'Branding & Creative Design Agency in Bhopal | Logo, Identity & Design',
        'meta_description'  => 'Branding and creative services in Bhopal — logo and identity systems, brand guidelines, messaging, decks, packaging and campaign creative.',
    ],

    /* ------------------------------------------------------------------ 06 */
    'it-services-cloud' => [
        'number'            => '06',
        'slug'              => 'it-services-cloud',
        'img'               => 'images/services/it-services-cloud.jpg',
        'name'              => 'IT & Cloud',
        'nav'               => 'IT & Cloud',
        'eyebrow'           => 'IT & CLOUD / 06 OF 08',
        'breadcrumb'        => 'Home / Services / IT & Cloud',
        'card_tag'          => 'Cloud setup, e-mail, security, backups and support that keep you running.',
        'icon'              => 'cloud',
        'visual'            => 'cloudv',
        'hero_title'        => 'Infrastructure that stays out of your way.',
        'hero_description'  => 'Cloud hosting, business e-mail, security, backups and managed IT support — so your team works without downtime surprises.',
        'quick_answer'      => 'IT and cloud services cover the technology a business runs on every day: servers and hosting, internet and office networking, business e-mail, device management, security, backups and support. We set it up properly and manage it quietly, so problems are prevented rather than discovered.',
        'outcome_label'     => 'THE OUTCOME',
        'outcome_title'     => 'Systems that are stable, secure and recoverable.',
        'outcome_description' => 'Clear hosting, configured mailboxes, tested backups, monitored uptime and a support channel with defined response times — documented so nothing depends on one person knowing a password.',
        'deliverables'      => [
            'Cloud and VPS hosting setup and migration',
            'Domains, DNS, SSL certificates and business e-mail',
            'Backups, monitoring and uptime alerting',
            'Security hardening, firewall and access control',
            'Office networking, devices and IT policy',
            'Managed support with a ticketing system',
        ],
        'without' => [
            'title'  => 'IT issues appear without warning.',
            'points' => [
                'Backups are assumed, never tested or documented.',
                'Passwords and accounts are shared informally.',
                'Slow hosting and e-mail deliverability hurt the brand.',
                'Recovery depends on one person who "knows the server".',
            ],
        ],
        'with' => [
            'title'  => 'Documented, monitored and maintained.',
            'points' => [
                'Tested backups with a stated recovery time.',
                'Role-based access and an offboarding checklist.',
                'Fast hosting, enforced HTTPS and deliverable e-mail.',
                'Monitoring that alerts us before users notice.',
            ],
        ],
        'process' => [
            ['number' => '01', 'title' => 'Audit the setup',    'description' => 'Hosting, e-mail, devices, security and gaps.'],
            ['number' => '02', 'title' => 'Design and document', 'description' => 'The target setup, written down before changes.'],
            ['number' => '03', 'title' => 'Migrate carefully',   'description' => 'Planned moves with rollback and zero-surprise timing.'],
            ['number' => '04', 'title' => 'Monitor and support', 'description' => 'Ongoing checks, alerts and defined response times.'],
        ],
        'faqs' => [
            ['q' => 'Can you migrate our website and e-mail without downtime?', 'a' => 'We migrate during low-traffic windows with a tested rollback plan. DNS and mail records are documented and copied before any change, so mail keeps flowing and the site stays available.'],
            ['q' => 'Do you provide on-site support in Bhopal?', 'a' => 'Yes. Remote support is handled through a ticketing system, and on-site visits are available across Bhopal and nearby districts for networking, hardware and office setup work.'],
            ['q' => 'What does managed support include?', 'a' => 'Monitoring and alerts, backup verification, security patching, mailbox and hosting administration, and a defined support channel with agreed response times.'],
        ],
        'cta_label'         => 'STABILISE YOUR SETUP',
        'cta_title'         => 'Let us review your IT setup.',
        'cta_description'   => 'Tell us what breaks most often and we will propose priorities and a timeline.',
        'meta_title'        => 'IT Services & Cloud Support in Bhopal | Hosting, Security, Backups',
        'meta_description'  => 'IT and cloud services in Bhopal — hosting, DNS, business e-mail, security hardening, backups, monitoring and managed IT support for growing businesses.',
    ],

    /* ------------------------------------------------------------------ 07 */
    'ai-automation' => [
        'number'            => '07',
        'slug'              => 'ai-automation',
        'img'               => 'images/services/ai-automation.jpg',
        'name'              => 'AI & Automation',
        'nav'               => 'AI & Automation',
        'eyebrow'           => 'AI & AUTOMATION / 07 OF 08',
        'breadcrumb'        => 'Home / Services / AI & Automation',
        'card_tag'          => 'Practical AI assistants and workflow automation that remove manual work.',
        'icon'              => 'cpu',
        'visual'            => 'ai',
        'hero_title'        => 'Automate the work nobody should do twice.',
        'hero_description'  => 'Practical AI assistants, workflow automation and integrations that cut manual effort across sales, support and operations.',
        'quick_answer'      => 'AI and automation means using software to do repetitive work: reading documents, answering routine questions, moving data between tools and triggering the next step automatically. We start with one measured process, prove the saving, and then automate the next one — keeping a human in the loop wherever accuracy matters.',
        'outcome_label'     => 'THE OUTCOME',
        'outcome_title'     => 'Fewer manual steps, faster responses, fewer errors.',
        'outcome_description' => 'Enquiries answered in seconds, documents processed automatically, and data entered once and shared everywhere — with logs so you can always see what the system did.',
        'deliverables'      => [
            'Chat and WhatsApp assistants trained on your content',
            'Document and invoice data extraction',
            'Workflow automation — form to CRM to invoice',
            'Integrations between your existing tools and APIs',
            'AI-assisted drafting, summaries and reporting',
            'Internal knowledge search for your team',
        ],
        'without' => [
            'title'  => 'People spend their day on software chores.',
            'points' => [
                'The same data is typed into three different systems.',
                'Enquiries wait hours for a first reply.',
                'Documents are read and keyed in by hand.',
                'Knowledge sits with individuals, not the business.',
            ],
        ],
        'with' => [
            'title'  => 'Routine work runs itself, with oversight.',
            'points' => [
                'Data entered once and synchronised automatically.',
                'Instant first response, then a human takes over.',
                'Documents processed and validated in seconds.',
                'Every automated action logged and reviewable.',
            ],
        ],
        'process' => [
            ['number' => '01', 'title' => 'Find the right process', 'description' => 'We pick work that is repetitive, measurable and safe.'],
            ['number' => '02', 'title' => 'Map the manual steps',   'description' => 'Every decision and exception written down.'],
            ['number' => '03', 'title' => 'Build and supervise',    'description' => 'Automation built with human review where needed.'],
            ['number' => '04', 'title' => 'Measure and expand',     'description' => 'Hours saved verified, then the next process automated.'],
        ],
        'faqs' => [
            ['q' => 'Will AI make mistakes with our data?', 'a' => 'We design for review, not blind trust. High-impact steps keep a human approval gate, low-risk steps run automatically, and every action is logged so errors are visible and correctable.'],
            ['q' => 'What can realistically be automated first?', 'a' => 'Usually lead capture and follow-up, quotation generation, invoice and document data entry, appointment reminders, support FAQs and internal reporting — processes with clear steps and repetition.'],
            ['q' => 'Where does our data live?', 'a' => 'Wherever you decide. We can run on your own server, on a private cloud instance or with a vendor API — and we document exactly what data leaves your systems and why.'],
        ],
        'cta_label'         => 'REMOVE THE BUSY WORK',
        'cta_title'         => 'Let us automate one process properly.',
        'cta_description'   => 'Describe the task your team repeats most and we will assess whether it is worth automating.',
        'meta_title'        => 'AI & Automation Services in Bhopal | Chatbots & Workflow Automation',
        'meta_description'  => 'AI and automation services in Bhopal — chat and WhatsApp assistants, document extraction, workflow automation and integrations that cut manual work.',
    ],

    /* ------------------------------------------------------------------ 08 */
    'growth-strategy' => [
        'number'            => '08',
        'slug'              => 'growth-strategy',
        'img'               => 'images/services/growth-strategy.jpg',
        'name'              => 'Growth Strategy',
        'nav'               => 'Growth',
        'eyebrow'           => 'GROWTH / 08 OF 08',
        'breadcrumb'        => 'Home / Services / Growth',
        'card_tag'          => 'Positioning, funnel design and channel plans built on your real numbers.',
        'icon'              => 'growth',
        'visual'            => 'analytics',
        'hero_title'        => 'A growth plan you can actually execute this quarter.',
        'hero_description'  => 'Positioning, funnel design, pricing and channel strategy — a roadmap built on your numbers, not on templates.',
        'quick_answer'      => 'Growth strategy is deciding where a business should focus next: which customers to serve, what to sell them, how the offer is priced and which channels will reach them profitably. The output is a prioritised plan with owners, budgets and numbers to watch — not a slide deck that sits unopened.',
        'outcome_label'     => 'THE OUTCOME',
        'outcome_title'     => 'Clear priorities, measurable in one quarter.',
        'outcome_description' => 'A written roadmap with three to five priorities, the metrics that prove progress, and the weekly rhythm to keep it moving after the consultant has left.',
        'deliverables'      => [
            'Market, competitor and customer research',
            'Positioning and offer design',
            'Funnel and conversion mapping',
            'Channel and budget planning',
            'KPI dashboards and review cadence',
            'Quarterly growth roadmaps with owners',
        ],
        'without' => [
            'title'  => 'Everything looks like a priority.',
            'points' => [
                'Decisions are made on instinct and anecdotes.',
                'Effort is spread across too many channels at once.',
                'Pricing and offers are never tested against the market.',
                'Nobody can say which activity moved the numbers.',
            ],
        ],
        'with' => [
            'title'  => 'A short list, owned and measured weekly.',
            'points' => [
                'Three to five priorities with named owners.',
                'One dashboard showing the numbers that matter.',
                'Offers and pricing tested, not assumed.',
                'A weekly rhythm that keeps the plan alive.',
            ],
        ],
        'process' => [
            ['number' => '01', 'title' => 'Review the numbers', 'description' => 'Revenue, funnel and channel data examined honestly.'],
            ['number' => '02', 'title' => 'Choose the battlefield', 'description' => 'Where you can win, and what to stop doing.'],
            ['number' => '03', 'title' => 'Build the roadmap',  'description' => 'Priorities, owners, budgets and timelines.'],
            ['number' => '04', 'title' => 'Run the cadence',    'description' => 'Weekly reviews that keep execution on track.'],
        ],
        'faqs' => [
            ['q' => 'Is strategy work useful for a small business?', 'a' => 'Especially for a small business — a limited budget makes focus more valuable. We keep engagement scoped to a few weeks and deliver a plan that the existing team can execute.'],
            ['q' => 'How is this different from marketing services?', 'a' => 'Strategy decides what to do and why; marketing executes it. Some clients take strategy first and run it in-house, others ask us to execute as well.'],
            ['q' => 'What do we receive?', 'a' => 'A written roadmap, a KPI dashboard definition, an offer and pricing recommendation, and a review cadence with templates your team keeps using.'],
        ],
        'cta_label'         => 'PLAN THE NEXT QUARTER',
        'cta_title'         => 'Let us find your next best move.',
        'cta_description'   => 'Share your revenue goal and current channels — we will suggest the sharpest path.',
        'meta_title'        => 'Growth Strategy Consulting in Bhopal | Positioning, Funnel & Channel Planning',
        'meta_description'  => 'Growth strategy services in Bhopal — market research, positioning, offer design, funnel mapping and channel plans with dashboards your team can run.',
    ],
];

/* =========================================================================
   PRODUCTS  (5)
   ========================================================================= */
$PD_PRODUCTS = [

    'routeflow' => [
        'slug'          => 'routeflow',
        'name'          => 'RouteFlow',
        'monogram'      => 'RF',
        'category'      => 'Logistics',
        'icon'          => 'layers',
        'visual'        => 'map',
        'tagline'       => 'Plan routes, track deliveries and bill customers from one dashboard.',
        'hero_title'    => 'Deliveries planned, tracked and billed in one place.',
        'hero_description' => 'RouteFlow is logistics software for transporters, distributors and delivery teams — routing, dispatch, proof of delivery and customer billing in a single system.',
        'quick_answer'  => 'RouteFlow is a logistics management platform built for Indian transport and distribution businesses. It plans and optimises delivery routes, gives drivers and dispatchers live visibility, captures proof of delivery on the spot, and generates customer bills from the trips that actually happened.',
        'features'      => [
            ['icon' => 'layers', 'title' => 'Route planning & dispatch', 'text' => 'Group orders into efficient routes, assign vehicles and release dispatch sheets to drivers.'],
            ['icon' => 'pin',    'title' => 'Live tracking & POD',        'text' => 'Follow trips on the map and capture delivery proof, signature or photo at the door.'],
            ['icon' => 'phone',  'title' => 'Driver app',                 'text' => 'A simple Android app for trip sheets, delivery updates and expense entries.'],
            ['icon' => 'chart',  'title' => 'Vehicle & fuel records',     'text' => 'Track vehicles, drivers, fuel and maintenance cost per trip and per kilometre.'],
            ['icon' => 'check',  'title' => 'Rate cards & billing',       'text' => 'Customer-specific rate cards turn completed trips into accurate invoices.'],
            ['icon' => 'growth', 'title' => 'Operations reporting',       'text' => 'On-time performance, delivery cost and route profitability in one dashboard.'],
        ],
        'without' => [
            'title'  => 'Dispatch runs on phone calls and memory.',
            'points' => [
                'Routes are assigned verbally and changed unpredictably.',
                'Nobody knows delivery status until the driver calls back.',
                'Proof of delivery is a paper slip that gets lost.',
                'Billing is rebuilt at month end from diaries and slips.',
            ],
        ],
        'with' => [
            'title'  => 'Dispatch, tracking and billing connected.',
            'points' => [
                'Routes planned and released to drivers in minutes.',
                'Live status visible to the office and the customer.',
                'Delivery proof captured digitally at the doorstep.',
                'Invoices generated from completed trips automatically.',
            ],
        ],
        'who_for' => [
            'Transporters and fleet owners',
            'Distributors and FMCG supply chains',
            'E-commerce and courier operations',
            'Field-service and installation teams',
        ],
        'faqs' => [
            ['q' => 'Does RouteFlow work without internet on the road?', 'a' => 'The driver app keeps working offline and synchronises trip updates and proof of delivery automatically once the device reconnects.'],
            ['q' => 'Can it connect to our billing or ERP software?', 'a' => 'Yes. RouteFlow exposes APIs and file exports for customers, rate cards, trips and invoices, so it can feed your existing accounting or ERP system.'],
        ],
        'cta_title'     => 'See RouteFlow on your own routes.',
        'cta_description' => 'Share your fleet size and delivery volume — we will walk you through a tailored demo.',
        'meta_title'    => 'RouteFlow — Logistics & Delivery Management Software | Prospect Digital',
        'meta_description' => 'RouteFlow by Prospect Digital: route planning, live delivery tracking, driver app, proof of delivery and customer billing for transporters and distributors in India.',
    ],

    'workora' => [
        'slug'          => 'workora',
        'name'          => 'Workora',
        'monogram'      => 'WK',
        'category'      => 'Office & Business',
        'icon'          => 'users',
        'visual'        => 'kanban',
        'tagline'       => 'Run projects, attendance and office paperwork without spreadsheets.',
        'hero_title'    => 'The daily running of the office, on one platform.',
        'hero_description' => 'Workora brings tasks, attendance, leave, expenses, documents and approvals together for teams that have outgrown spreadsheets and WhatsApp groups.',
        'quick_answer'  => 'Workora is office and business management software for growing teams. It handles work allocation, attendance and leave, expense claims, approvals and HR documents — with dashboards for managers and a simple mobile experience for staff.',
        'features'      => [
            ['icon' => 'check', 'title' => 'Tasks & projects',      'text' => 'Boards, owners, due dates and status so work is visible without follow-up calls.'],
            ['icon' => 'clock', 'title' => 'Attendance & leave',     'text' => 'Daily attendance, shifts, leave requests and approvals with a full history.'],
            ['icon' => 'chart', 'title' => 'Invoicing & expenses',   'text' => 'Raise invoices, record expenses and keep claims tied to approvals.'],
            ['icon' => 'users', 'title' => 'HR records & documents', 'text' => 'Employee profiles, letters, policies and documents stored securely.'],
            ['icon' => 'shield','title' => 'Approvals & roles',      'text' => 'Multi-level approval flows with role-based access for every team.'],
            ['icon' => 'growth','title' => 'Management dashboards',  'text' => 'Utilisation, pending work and cost summaries for owners and managers.'],
        ],
        'without' => [
            'title'  => 'Office work tracked in ten spreadsheets.',
            'points' => [
                'Attendance and leave live in registers and WhatsApp messages.',
                'Approvals wait until the manager is in the office.',
                'Documents are on individual laptops, not the company.',
                'Nobody has a reliable view of who is doing what.',
            ],
        ],
        'with' => [
            'title'  => 'One place for work, people and paperwork.',
            'points' => [
                'Attendance, leave and shifts captured digitally.',
                'Approvals completed from a phone, with an audit trail.',
                'Company documents centralised with access control.',
                'Workload and pending items visible at a glance.',
            ],
        ],
        'who_for' => [
            'Small and mid-sized offices',
            'Agencies and consultancies',
            'Manufacturing and trading firms',
            'Multi-branch service businesses',
        ],
        'faqs' => [
            ['q' => 'Can staff use Workora on their phones?', 'a' => 'Yes. The mobile experience is designed for attendance, leave, approvals and task updates, while the full management view works on desktop.'],
            ['q' => 'Can we control who sees what?', 'a' => 'Workora uses role-based access, so owners, managers, HR and staff each see only the modules and data relevant to their role.'],
        ],
        'cta_title'     => 'Bring your office onto Workora.',
        'cta_description' => 'Tell us your team size and the processes you want to move off spreadsheets.',
        'meta_title'    => 'Workora — Office & Business Management Software | Prospect Digital',
        'meta_description' => 'Workora by Prospect Digital: tasks, attendance, leave, expenses, approvals, HR documents and management dashboards for growing teams.',
    ],

    'bizora' => [
        'slug'          => 'bizora',
        'name'          => 'Bizora',
        'monogram'      => 'BZ',
        'category'      => 'Customer Relationship Management',
        'icon'          => 'target',
        'visual'        => 'table',
        'tagline'       => 'Every lead, call and follow-up in one place — nothing forgotten.',
        'hero_title'    => 'Never lose a deal to a forgotten follow-up.',
        'hero_description' => 'Bizora is CRM software built for Indian sales teams — lead capture, pipeline stages, follow-up reminders, quotations and sales reporting.',
        'quick_answer'  => 'Bizora is customer relationship management software that keeps every enquiry, conversation and follow-up in one pipeline. Leads arrive from your website, calls, WhatsApp and referrals, are assigned to the right person, and move stage by stage until they close — with reminders so nothing slips.',
        'features'      => [
            ['icon' => 'search', 'title' => 'Lead capture from everywhere', 'text' => 'Website forms, calls, WhatsApp, walk-ins and referrals all land in one inbox.'],
            ['icon' => 'layers', 'title' => 'Visual sales pipeline',          'text' => 'Drag deals through stages with value, owner and expected close date.'],
            ['icon' => 'phone',  'title' => 'Call & message history',         'text' => 'Every call, note and message stored against the customer record.'],
            ['icon' => 'clock',  'title' => 'Follow-up reminders',            'text' => 'Automatic next-action reminders so hot leads do not go cold.'],
            ['icon' => 'check',  'title' => 'Quotations & documents',         'text' => 'Branded quotations and proposals generated from deal data.'],
            ['icon' => 'chart',  'title' => 'Sales reporting',                'text' => 'Conversion by source, stage and salesperson, with forecast view.'],
        ],
        'without' => [
            'title'  => 'Leads depend on personal memory.',
            'points' => [
                'Enquiries arrive in different inboxes and get missed.',
                'Follow-ups depend on someone remembering to call.',
                'Sales performance cannot be compared fairly.',
                'Leads are lost when a salesperson leaves.',
            ],
        ],
        'with' => [
            'title'  => 'One pipeline the whole team can trust.',
            'points' => [
                'Every enquiry captured and assigned within minutes.',
                'Reminders drive the next action automatically.',
                'Conversion by source and salesperson measured honestly.',
                'Customer history belongs to the company, not an individual.',
            ],
        ],
        'who_for' => [
            'B2B sales teams',
            'Real estate and property businesses',
            'Education and admission teams',
            'Service and dealership businesses',
        ],
        'faqs' => [
            ['q' => 'Can Bizora take leads directly from our website?', 'a' => 'Yes. Website forms, landing pages, missed-call services and WhatsApp enquiries can all push leads into Bizora automatically with the source tracked.'],
            ['q' => 'Does it work for a small sales team?', 'a' => 'Bizora is used by teams of two to two hundred. Small teams usually start with lead capture, pipeline and reminders, then add quotations and automation.'],
        ],
        'cta_title'     => 'See your pipeline inside Bizora.',
        'cta_description' => 'Tell us how leads reach you today and we will map them into a pipeline demo.',
        'meta_title'    => 'Bizora — CRM Software for Sales Teams in India | Prospect Digital',
        'meta_description' => 'Bizora by Prospect Digital: lead capture, sales pipeline, follow-up reminders, quotations and reporting in CRM software built for Indian sales teams.',
    ],

    'medvora' => [
        'slug'          => 'medvora',
        'name'          => 'Medvora',
        'monogram'      => 'MV',
        'category'      => 'Hospital Management',
        'icon'          => 'shield',
        'visual'        => 'calendar',
        'tagline'       => 'Front desk to pharmacy on a single patient record.',
        'hero_title'    => 'Patient records that stay with the patient.',
        'hero_description' => 'Medvora is hospital and clinic management software covering registration, appointments, consultations, pharmacy, diagnostics and billing.',
        'quick_answer'  => 'Medvora is hospital management software that keeps one patient record across the whole facility — registration, appointments, doctor consultations, pharmacy, lab tests, billing and follow-up. Staff work in the modules they need, and the patient never has to repeat their history at every counter.',
        'features'      => [
            ['icon' => 'users',  'title' => 'OPD & IPD registration', 'text' => 'Quick registration, UHID generation, bed and ward allocation.'],
            ['icon' => 'clock',  'title' => 'Appointments & queue',    'text' => 'Doctor schedules, token queues and reminders for patients.'],
            ['icon' => 'check',  'title' => 'Consultation records',    'text' => 'Vitals, diagnosis, prescriptions and notes in a structured record.'],
            ['icon' => 'layers', 'title' => 'Pharmacy & inventory',    'text' => 'Batch-wise stock, expiry alerts and dispensing against prescriptions.'],
            ['icon' => 'search', 'title' => 'Lab & diagnostics',       'text' => 'Test orders, sample tracking, report generation and delivery.'],
            ['icon' => 'chart',  'title' => 'Billing & insurance',     'text' => 'OPD, IPD, TPA and insurance billing with daily collection reports.'],
        ],
        'without' => [
            'title'  => 'Counters work in isolation.',
            'points' => [
                'Patient history is a paper file that may not be found.',
                'Pharmacy stock and expiry discovered physically.',
                'Billing disputes take hours to reconcile.',
                'Management has no daily view of revenue or occupancy.',
            ],
        ],
        'with' => [
            'title'  => 'One record across every department.',
            'points' => [
                'Any authorised counter can see the patient history instantly.',
                'Pharmacy stock and expiry tracked with alerts.',
                'Billing built from services actually delivered.',
                'Daily dashboards for occupancy, footfall and collections.',
            ],
        ],
        'who_for' => [
            'Multi-speciality hospitals',
            'Nursing homes and clinics',
            'Diagnostic centres',
            'Ayurveda and specialty care centres',
        ],
        'faqs' => [
            ['q' => 'Can Medvora handle multiple branches?', 'a' => 'Yes. Branches can share the master patient index while keeping their own stock, billing and staff access separate, with consolidated reporting for management.'],
            ['q' => 'Is patient data secure?', 'a' => 'Access is role-based with full audit trails, data is encrypted in transit, and backups run on a documented schedule. We work with your team on consent and retention policy.'],
        ],
        'cta_title'     => 'Book a Medvora walkthrough.',
        'cta_description' => 'Tell us your bed count and departments — we will show the workflows that matter to you.',
        'meta_title'    => 'Medvora — Hospital Management Software in India | Prospect Digital',
        'meta_description' => 'Medvora by Prospect Digital: hospital and clinic management software for OPD/IPD registration, appointments, pharmacy, diagnostics and billing.',
    ],

    'schova' => [
        'slug'          => 'schova',
        'name'          => 'Schova',
        'monogram'      => 'SV',
        'category'      => 'School Management',
        'icon'          => 'users',
        'visual'        => 'calendar',
        'tagline'       => 'Admissions, attendance, fees and parent updates in one system.',
        'hero_title'    => 'From admission enquiry to report card, connected.',
        'hero_description' => 'Schova is school management software for admissions, attendance, fees, examinations and parent communication — with a practical parent app.',
        'quick_answer'  => 'Schova is school ERP software that runs the administrative side of a school: admission enquiries, student records, daily attendance, fee collection and dues, examinations and report cards, transport, and messaging to parents. Teachers get back time, the office stops chasing paperwork, and parents stay informed.',
        'features'      => [
            ['icon' => 'check',  'title' => 'Admissions & enquiry',   'text' => 'Enquiry pipeline, admission forms, documents and seat confirmation.'],
            ['icon' => 'users',  'title' => 'Student records',         'text' => 'Complete profiles, class and section history in one searchable place.'],
            ['icon' => 'clock',  'title' => 'Attendance tracking',     'text' => 'Class-wise, biometric or app-based attendance with SMS/WhatsApp alerts.'],
            ['icon' => 'chart',  'title' => 'Fees & dues',             'text' => 'Fee structures, instalments, receipts, concessions and defaulter lists.'],
            ['icon' => 'search', 'title' => 'Exams & report cards',    'text' => 'Marks entry, grade rules, printable report cards and result analysis.'],
            ['icon' => 'layers', 'title' => 'Transport & hostel',      'text' => 'Route allocation, pickup points and hostel room management.'],
        ],
        'without' => [
            'title'  => 'The office runs on registers and reminders.',
            'points' => [
                'Admission enquiries are tracked in a notebook.',
                'Fee dues are discovered while preparing year-end accounts.',
                'Parents call the office for information that should be automatic.',
                'Report cards take days of manual calculation.',
            ],
        ],
        'with' => [
            'title'  => 'Administration on autopilot, parents informed.',
            'points' => [
                'Every admission enquiry followed up systematically.',
                'Fee collection, dues and receipts always current.',
                'Parents informed by app and message without phone calls.',
                'Report cards generated from entered marks in minutes.',
            ],
        ],
        'who_for' => [
            'CBSE and State board schools',
            'Play schools and primary schools',
            'Coaching institutes',
            'Residential and boarding schools',
        ],
        'faqs' => [
            ['q' => 'Do parents need to install an app?', 'a' => 'A parent app gives access to attendance, fees, results, homework and circulars. Parents who prefer not to install it still receive updates by SMS or WhatsApp.'],
            ['q' => 'Can Schova handle our fee structure exceptions?', 'a' => 'Yes. Concessions, sibling discounts, instalments, transport fees and late fine rules can be configured per class, per student or per branch.'],
        ],
        'cta_title'     => 'See Schova with your school data.',
        'cta_description' => 'Share your student strength and boards — we will show the modules your office needs most.',
        'meta_title'    => 'Schova — School Management Software in India | Prospect Digital',
        'meta_description' => 'Schova by Prospect Digital: school ERP for admissions, student records, attendance, fees, examinations, transport and parent communication.',
    ],
];

/* =========================================================================
   HOMEPAGE CONTENT
   ========================================================================= */
$PD_HOME = [
    'hero' => [
        'eyebrow'     => 'Digital solutions · Bhopal, India',
        'title'       => 'We build digital solutions that deliver business growth.',
        'highlight'   => 'Build. Grow. Scale.',
        'description' => 'Software, websites, cloud and marketing that make a measurable impact — designed and supported by one accountable team in Bhopal, for businesses across India.',
        'primary_cta' => ['label' => 'Get a free consultation', 'url' => 'contact'],
        'secondary_cta' => ['label' => "Let's talk", 'url' => 'contact'],
        'points'      => [
            'Free scoping call with a senior team member',
            'Fixed scope, timeline and cost before we start',
            'Support that continues after launch',
        ],
    ],

    'stats' => [
        ['value' => '8',     'label' => 'Service lines under one roof'],
        ['value' => '5',     'label' => 'Platforms we build and run'],
        ['value' => '100%',  'label' => 'Projects delivered in stages'],
        ['value' => 'Bhopal','label' => 'Head office, working across India'],
    ],

    'outcomes' => [
        [
            'icon'  => 'growth',
            'title' => 'Revenue that is easier to forecast',
            'text'  => 'Clear pipelines, faster follow-ups and channels you can measure — so growth stops being a guess.',
        ],
        [
            'icon'  => 'clock',
            'title' => 'Hours returned to your team',
            'text'  => 'Repetitive data entry, manual reporting and double work removed through software and automation.',
        ],
        [
            'icon'  => 'shield',
            'title' => 'Systems you can rely on',
            'text'  => 'Documented hosting, tested backups, role-based access and support with defined response times.',
        ],
        [
            'icon'  => 'chart',
            'title' => 'Decisions backed by data',
            'text'  => 'Dashboards and reports that answer the questions owners actually ask every week.',
        ],
    ],

    'positioning' => [
        'eyebrow'     => 'Why Prospect Digital',
        'title'       => 'One accountable team instead of five vendors.',
        'description' => 'Most businesses juggle a website freelancer, a software vendor, a marketing agency and an IT support company — and then spend their time coordinating between them. We bring strategy, build and growth into a single team that answers for the result.',
        'points'      => [
            ['title' => 'Senior involvement end to end', 'text' => 'The people who scope your project are the people who deliver and support it.'],
            ['title' => 'Business language, not jargon', 'text' => 'We explain trade-offs in terms of time, cost and risk — before any work starts.'],
            ['title' => 'Fixed milestones you can see',  'text' => 'Work is delivered in stages with review points, so you always know where things stand.'],
            ['title' => 'Built to be handed over',       'text' => 'Documentation, training and clean handover — you are never locked in to us.'],
        ],
        'cta' => ['label' => 'More about us', 'url' => 'about'],
    ],

    'process' => [
        ['number' => '01', 'title' => 'Discover',  'text' => 'A conversation about goals, constraints, budget and what success looks like.'],
        ['number' => '02', 'title' => 'Define',    'text' => 'Scope, timeline, cost and responsibilities written down and approved.'],
        ['number' => '03', 'title' => 'Deliver',   'text' => 'Work shipped in stages with reviews, so feedback arrives early.'],
        ['number' => '04', 'title' => 'Develop',   'text' => 'After launch we measure, train, support and improve the result.'],
    ],

    'faqs' => [
        ['q' => 'What does a first conversation involve?', 'a' => 'A 30–45 minute call or meeting where we understand your business, what you need and roughly what budget is available. You leave with a clear recommendation — and there is no charge for it.'],
        ['q' => 'Do you work with businesses outside Bhopal?', 'a' => 'Yes. Clients across India work with us remotely through scheduled calls and shared project trackers, while local clients in and around Bhopal can meet at our M.P. Nagar office.'],
        ['q' => 'Are you a product company or an agency?', 'a' => 'Both. We build and run our own platforms — RouteFlow, Workora, Bizora, Medvora and Schova — and we take on custom development, websites and marketing for clients. Product experience makes our custom work more practical.'],
        ['q' => 'How do payments and contracts work?', 'a' => 'Projects are milestone-based with a written scope: usually an advance to schedule the work and the balance across agreed milestones. Marketing retainers are billed monthly in advance.'],
    ],
];

/* =========================================================================
   ABOUT PAGE CONTENT
   ========================================================================= */
$PD_ABOUT = [
    'hero' => [
        'eyebrow'     => 'About Prospect Digital',
        'title'       => 'A digital partner built for how Indian businesses actually grow.',
        'description' => 'We are a Bhopal-based team of developers, designers and marketers who build software and growth systems for companies that have outgrown spreadsheets, disconnected tools and one-off vendors.',
    ],
    'story' => [
        'Prospect Digital began with a simple observation: most growing businesses in India do not need more technology — they need technology that fits. The tools available were either too generic to match real workflows or too expensive and complex for a mid-sized team to run.',
        'So we work the other way around. We start with how your business already operates — how enquiries arrive, how work is dispatched, how invoices are raised, how decisions get made — and then design the website, software, cloud setup or marketing engine that makes those steps faster and more reliable.',
        'Today we build and run five of our own platforms alongside client work in software development, websites, IT and cloud, AI automation and growth. That mix keeps us honest: everything we recommend, we have to operate ourselves.',
    ],
    'values' => [
        ['icon' => 'shield',  'title' => 'Clarity before code',      'text' => 'Scope, cost and timeline are written down before work starts. No surprises in week six.'],
        ['icon' => 'check',   'title' => 'Practical over impressive', 'text' => 'We recommend the simplest solution that solves the problem — not the longest list of features.'],
        ['icon' => 'users',   'title' => 'One accountable team',      'text' => 'The people who scope your work deliver and support it. You always know who to call.'],
        ['icon' => 'growth',  'title' => 'Measured outcomes',         'text' => 'We define what success means in numbers, then report against it honestly.'],
        ['icon' => 'cpu',     'title' => 'Built to be handed over',   'text' => 'Documentation and training are part of delivery. You own what we build.'],
        ['icon' => 'clock',   'title' => 'Support after launch',      'text' => 'Software and websites need care. We stay available with defined response times.'],
    ],
    'offices' => [
        ['label' => 'Registered office', 'value' => 'M.P. Nagar, Bhopal — Madhya Pradesh 462011'],
        ['label' => 'Working hours',     'value' => 'Monday to Saturday, 10:00 AM – 7:00 PM IST'],
        ['label' => 'Service area',      'value' => 'Bhopal, Indore, Jabalpur and clients across India'],
    ],
];

/* =========================================================================
   SELECTED WORK (projects.php)
   The five platforms below are product builds by Prospect Digital.
   The engagement snapshots are sector-level summaries — replace them with
   named client case studies once permissions are in place.
   ========================================================================= */
$PD_WORK = [
    'platforms' => ['routeflow', 'workora', 'bizora', 'medvora', 'schova'],
    'snapshots' => [
        [
            'sector'  => 'Manufacturing',
            'scope'   => 'Custom ERP — production, stock and billing',
            'result'  => 'Sales, stock and invoicing moved onto one system with daily production reporting.',
            'services' => ['software-development', 'it-services-cloud'],
        ],
        [
            'sector'  => 'Education',
            'scope'   => 'School ERP rollout and parent communication',
            'result'  => 'Admissions, attendance and fees digitised across sections, with automated parent updates.',
            'services' => ['software-development', 'ai-automation'],
        ],
        [
            'sector'  => 'Healthcare',
            'scope'   => 'Clinic management and billing workflows',
            'result'  => 'OPD registration to pharmacy connected on a single patient record.',
            'services' => ['software-development', 'branding-creative'],
        ],
        [
            'sector'  => 'Real Estate',
            'scope'   => 'Lead pipeline and campaign landing pages',
            'result'  => 'Enquiries captured from ads into one pipeline with follow-up reminders for the sales team.',
            'services' => ['performance-marketing', 'website-development'],
        ],
        [
            'sector'  => 'Retail & Distribution',
            'scope'   => 'E-commerce store and local search visibility',
            'result'  => 'Product catalogue launched with structured data and local listings feeding enquiries.',
            'services' => ['website-development', 'digital-marketing'],
        ],
        [
            'sector'  => 'Professional Services',
            'scope'   => 'Brand identity and website rebuild',
            'result'  => 'One consistent identity across proposals, website and campaigns, with a reusable template set.',
            'services' => ['branding-creative', 'website-development'],
        ],
    ],
];

/* =========================================================================
   GUIDES (guides.php) — short practical reads for buyers
   ========================================================================= */
$PD_GUIDES = [
    [
        'title' => 'How to decide between custom software and off-the-shelf tools',
        'text'  => 'A short framework based on process complexity, budget and how much your workflow differs from the standard.',
        'read'  => 'software-development',
        'tag'   => 'Software',
    ],
    [
        'title' => 'What a business website actually needs in 2026',
        'text'  => 'The pages, performance targets and enquiry paths that matter — and the features that can wait.',
        'read'  => 'website-development',
        'tag'   => 'Websites',
    ],
    [
        'title' => 'A realistic 90-day plan for local search visibility',
        'text'  => 'Google Business Profile, on-page basics, reviews and content: what to do in which order.',
        'read'  => 'digital-marketing',
        'tag'   => 'Marketing',
    ],
    [
        'title' => 'How to judge a paid ads report',
        'text'  => 'The five numbers to check before you accept a performance report — and the ones that hide problems.',
        'read'  => 'performance-marketing',
        'tag'   => 'Paid Ads',
    ],
    [
        'title' => 'Preparing your IT setup for a growing team',
        'text'  => 'Backups, access control, e-mail deliverability and documentation before you hire the next ten people.',
        'read'  => 'it-services-cloud',
        'tag'   => 'IT & Cloud',
    ],
    [
        'title' => 'Which process should you automate first?',
        'text'  => 'How to score repetitive work by frequency, error cost and measurability to pick a sensible first project.',
        'read'  => 'ai-automation',
        'tag'   => 'AI & Automation',
    ],
];

/* =========================================================================
   FAQ (privacy / terms helper copy)
   ========================================================================= */
$PD_LEGAL = [
    'last_updated' => '12 September 2026',
];
