<?php
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

function adminPages(){
    $seo=[
        ['Настройки','account.admin.seo.settings']
    ];

   

    $pages=[

        ['Захоронения','storage/uploads/mdi_grave-stone (1).svg',
            [
                ['Импортировать','account.admin.burial.parser'],
            ],
        ],

        ['Кладбища','storage/uploads/mdi_grave-stone (1).svg',
            [
                ['Импортировать','account.admin.parser.cemetery'],
                // ['Добавить кладбища','account.admin.create.cemetery']
            ],
        ],
        ['Морги','storage/uploads/game-icons_morgue-feet (2).svg',
            [
                ['Импортировать','account.admin.parser.mortuary'],
                // ['Добавить кладбища','account.admin.create.cemetery']
            ],
        ],
        ['Крематории','storage/uploads/emojione-monotone_funeral-urn.svg',
            [
                ['Импортировать','account.admin.parser.crematorium'],
                // ['Добавить кладбища','account.admin.create.cemetery']
            ],
        ],
        ['Колумбарии','storage/uploads/mdi_grave-stone (2).svg',
            [
                ['Импортировать','account.admin.parser.columbarium'],
                // ['Добавить кладбища','account.admin.create.cemetery']
            ],
        ],

        ['Организации','storage/uploads/mdi_grave-stone (2).svg',
            [
                ['Импортировать','account.admin.parser.organization'],
                // ['Добавить кладбища','account.admin.create.cemetery']
            ],
        ],

        ['География','storage/uploads/mdi_grave-stone (2).svg',
            [
                ['Импортировать','account.admin.parser.geo'],
            ],
        ],

        ['Продукты','storage/uploads/mdi_grave-stone (2).svg',
            [
                ['Импортировать','account.admin.parser.product'],
            ],
        ],
        
        ['Церкви','storage/uploads/mdi_grave-stone (2).svg',
            [
                ['Импортировать','account.admin.parser.church'],
            ],
        ],
        
        ['Мечети','storage/uploads/mdi_grave-stone (2).svg',
            [
                ['Импортировать','account.admin.parser.mosque'],
            ],
        ],

        ['Настройки общие','storage/uploads/mdi_grave-stone (2).svg',
            [
                ['Robots.txt','account.admin.settings-site.robots-txt']
            ]
        ],


       

        ['SEO','storage/uploads/mdi_grave-stone (2).svg',
            $seo
        ],
        
       
    ];
    return $pages;
}




function userPages(){
    $pages=[
        
        ['Настройки','storage/uploads/icon_sidebar.svg',
           [
            ['Настройки','account.user.settings']
           ]
        ],
        ['Геолокации','storage/uploads/icon_sidebar.svg',
           [
            ['Список','account.user.burial'],
            ['Избранное','account.user.burial.favorite'],
           ]
        ],

        ['Заказаы с маркетплэйса','storage/uploads/icon_sidebar.svg',
            [
                ['Заказы','account.user.products'],
            ]
        ],

        ['Услуги','storage/uploads/icon_sidebar.svg',
            [
                ['Список','account.user.services.index'],
            ]
        ],

        ['Поиск могил','storage/uploads/icon_sidebar.svg',
            [
                ['Список','account.user.burial-request.index'],
            ]
        ],


         ['Кошельки','storage/uploads/icon_sidebar.svg',
            [
                ['Кошельки','account.user.wallets'],
            ]
        ],


        ['Техподдержка','storage/uploads/icon_sidebar.svg',
            [
                ['Главная','account.tickets.index'],
                ['Создать заявку','account.tickets.create'],
            ]
        ],

        
    ];
    return $pages;

}


function agentPages(){
    $pages=[
        
        ['Настройки','storage/uploads/icon_sidebar.svg',
           [
            ['Настройки','account.agent.settings']
           ]
        ],


        ['Услуги','storage/uploads/icon_sidebar.svg',
            [
                ['Список','account.agent.services.index'],
            ]
        ],

 
        
    ];
    return $pages;

}


function organizationPages(){
    $user = user();
    $userId = $user ? $user->id : null;
    $organizationId = $user && $user->organizations->first() ? $user->organizations->first()->id : null;
    
    // Инициализируем счетчики
    $reviewCounts = [
        'organization_reviews' => 0,  // Отзывы об организации
        'product_comments' => 0,      // Комментарии к товарам
        'total' => 0                  // Общее количество
    ];
    
    $notificationCounts = [
        'reviews' => 0,
        'applications' => 0,
        'services' => 0,
        'calls' => 0,
        'total' => 0
    ];


    if ($userId) {
        // Счетчик тикетов (только для пользователя)
        $ticketCount = \App\Models\Notification::where('user_id', $userId)
            ->whereIn('type', [
                'ticket_reply',
                'ticket_status', 
                'ticket_closed',
                'ticket_assigned',
                'ticket_internal_reply'
            ])
            ->where('is_read', false)
            ->count();
        
        $notificationCounts['tickets'] = $ticketCount;
    }
    
    // Если есть организация, получаем реальные счетчики
    if ($organizationId) {
        // Счетчики отзывов и комментариев
        $reviewCounts['organization_reviews'] = \App\Models\Notification::where('organization_id', $organizationId)
            ->where('type', 'review')
            ->where('is_read', false)
            ->count();
            
        $reviewCounts['product_comments'] = \App\Models\Notification::where('organization_id', $organizationId)
            ->where('type', 'comment')
            ->where('is_read', false)
            ->count();
            
        $reviewCounts['total'] = $reviewCounts['organization_reviews'] + $reviewCounts['product_comments'];
        
        // Другие счетчики
        $notificationCounts['reviews'] = $reviewCounts['total'];

        $notificationCounts['orders'] = \App\Models\Notification::where('organization_id', $organizationId)
            ->whereIn('type', ['order_product'])
            ->where('is_read', false)
            ->count();
            
        $notificationCounts['beautification'] = \App\Models\Notification::where('organization_id', $organizationId)
            ->whereIn('type', ['beautification_new'])
            ->where('is_read', false)
            ->count();

        $notificationCounts['funeral_service'] = \App\Models\Notification::where('organization_id', $organizationId)
            ->whereIn('type', ['funeral_service_new'])
            ->where('is_read', false)
            ->count();
            
        $notificationCounts['calls'] = \App\Models\Notification::where('organization_id', $organizationId)
            ->where('type', 'call')
            ->where('is_read', false)
            ->count();
            
        // Общее количество (личные + организационные)
        $notificationCounts['total'] = \App\Models\Notification::where(function($query) use ($userId, $organizationId) {
                $query->where('user_id', $userId)
                      ->orWhere(function($q) use ($organizationId) {
                          $q->where('organization_id', $organizationId)
                            ->whereNull('user_id');
                      });
            })
            ->where('is_read', false)
            ->count();
    }

    $organizations = $user ? $user->organizations : [];
    $ul_organizations = [];
    foreach($organizations as $organization){
        $ul_organizations[] = [$organization->title.' '.$organization->adres, 'account.agency.organization.settings', $organization->id];
    }
    $ul_organizations[] = ['Привязать организацию', 'account.agency.add.organization'];
    $ul_organizations[] = ['Создать организацию', 'account.agency.organization.create-page'];

    $pages = [
        ['Организации', 'storage/uploads/Icon_sidebar_2.svg',
            $ul_organizations
        ],

        ['Заявки по умершему', 'storage/uploads/Vector (17).svg',
            [
                ['Новые', 'account.agency.organization.aplication.dead.new'],  
                ['В работе', 'account.agency.organization.aplication.dead.in-work'],                
                ['Завершенные', 'account.agency.organization.aplication.dead.completed'],                
            ],
            'applications'
        ],

        ['Заявки по поминкам', 'storage/uploads/Vector (17).svg',
            [
                ['Новые', 'account.agency.organization.aplication.memorial.new'], 
                ['В работе', 'account.agency.organization.aplication.memorial.in-work'],
                ['Завершенные', 'account.agency.organization.aplication.memorial.completed'],                
            ],
            'applications'
        ],

        ['Заявки по благоустройству', 'storage/uploads/Vector (17).svg',
            [
                ['Новые', 'account.agency.organization.aplication.beautification.new'],                
                ['В работе', 'account.agency.organization.aplication.beautification.in-work'],  
                ['Завершенные', 'account.agency.organization.aplication.beautification.completed'],         
            ],
            'beautification'
        ],

        ['Заявки по ритуальным услугам', 'storage/uploads/Vector (17).svg',
            [
                ['Новые', 'account.agency.organization.aplication.funeral-service.new'],    
                ['В работе', 'account.agency.organization.aplication.funeral-service.in-work'], 
                ['Завершенные', 'account.agency.organization.aplication.funeral-service.completed'],                
            ],
            'funeral_service'
        ],

        ['Звонки', 'storage/uploads/Icon_calls.svg',
           [
            ['Входящие', 'account.agency.organization.calls.stats']
           ],
           'calls'
        ],

        ['Статистика', 'storage/uploads/Icon_sidebar_2.svg',
           [
            ['Визиты', 'account.agency.organization.statistics.sessions']
           ]
        ],

        ['Подчиненные', 'storage/uploads/Icon_sidebar_2.svg',
           [
                ['Список', 'account.agency.users']
           ]
        ],

        ['Техподдержка', 'storage/uploads/icon_sidebar.svg',
            [
                ['Главная', 'account.agency.tickets.index'],
                ['Создать заявку', 'account.agency.tickets.create'],
            ],
            'tickets'
        ],
        
        ['Настройки', 'storage/uploads/icon_sidebar.svg',
           [
            ['Настройки', 'account.agency.settings']
           ]
        ],

        ['Оплаты', 'storage/uploads/Icon_pay_aplication.svg',
            [
                ['Кошельки', 'account.agency.organization.wallets'],
                ['Заявки', 'account.agency.applications'],
                ['Приоритет', 'account.agency.priority.buy']
            ]
        ],

        ['Товар', 'storage/uploads/Icon_sidebar_2.svg',
            [
                ['Создать товар', 'account.agency.add.product'],
                ['Все товары', 'account.agency.products'],
            ]
        ],

        ['Отзывы', 'storage/uploads/Icon_reviews.svg',
            [
                // Добавляем счетчики к названиям подпунктов
                ['Об организации', 'account.agency.reviews.organization', null, $reviewCounts['organization_reviews']],
                ['О товарах', 'account.agency.reviews.product', null, $reviewCounts['product_comments']],
            ],
            'reviews' // Для основного пункта меню используем общее количество
        ],

        ['Заказы с маркетплейса', 'storage/uploads/Icon_sidebar_2.svg',
            [
                ['Новые', 'account.agency.product.orders.new'],            
                ['В работе', 'account.agency.product.orders.in-work'],                
                ['Завершенные', 'account.agency.product.orders.completed'],                
            ],
            'orders'
        ],

        ['Поставщики', 'storage/uploads/Icon_provider.svg',
            [
                ['Заявки', 'account.agency.provider.requests.products.add'],
                ['Созданные заявки', 'account.agency.provider.requests.products.created'],
                ['Ответы на заявки', 'account.agency.provider.requests.products.answer'],
                ['Избранное', 'account.agency.provider.like.organizations'],
                ['Акции', 'account.agency.provider.stocks'],
                ['Скидки', 'account.agency.provider.discounts'],
                ['Создание запроса', 'account.agency.provider.offer.add'],
                ['Созданные запросы', 'account.agency.provider.offer.created'],
                ['Ответы на запросы', 'account.agency.provider.offer.answers'],  
            ]
        ],
    ];
    
    // Добавляем счетчики в массив страниц
    foreach ($pages as &$page) {
        $page['notification_count'] = 0;
        if (isset($page[3]) && $page[3]) { // Если есть 4-й элемент (тип уведомлений)
            $type = $page[3];
            $page['notification_count'] = $notificationCounts[$type] ?? 0;
        }
    }
    
    // Сохраняем детальные счетчики для отзывов
    $pages['review_details'] = $reviewCounts;
    
    return $pages;
}

function mobilePages() {
    $pages = [
        [
            ['Главная', route('index')],
        ],
        
        [
            ['Поиск могил', ''],
            [
                ['Поиск', route('search.burial')],
                ['Герои', route('page.search.burial.filter')],
                ['Заявка на поиск', route('page.search.burial.request')],
            ],
        ],
        
        [
            ['Ритуальные услуги', ''],
            [
                ['Ритуальные агенства', route('organizations.category', 'organizacia-pohoron')],
                ['Ритуальные товары, услуги', route('marketplace.category', 'organizacia-pohoron')],
            ],
        ],
        
        [
            ['Ритуальные объекты', ''],
            [
                ['Кладбища', route('cemeteries')],
                ['Морги', route('mortuaries')],
            ],
        ],
        
        [
            ['Информация', ''],
            [
                ['Наши работы', route('our.products')],
                ['Статьи', route('news')],
                ['Контакты', route('contacts')],
            ],
        ],
    ];

    return $pages;
}


function mobilePagesAccountUser(){
    return [
        
        [
            ['Главная',route('home')],
            
        ],

        [
            ['Настройки',route('account.user.settings')],
            
        ],
    
        [
            ['Геолокации',''],

            [
                ['Список',route('account.user.burial')],
                ['Избранное',route('account.user.burial.favorite')],
            ],
        ],


        [
            ['Кошельки',route('account.user.wallets')],
        ],

        [
            ['Заказаы с маркетплэйса',route('account.user.products')],
        ],

        [
            ['Услуги',route('account.user.services.index')],
        ],


        [
            ['Поиск могил',route('account.user.burial-request.index')],
        ],

        
    
    ];
}


function mobilePagesAccountDecoder(){
    return [
        
        [
            ['Главная',route('home')],
            
        ],

        [
            ['Настройки',route('account.decoder.settings')],
            
        ],

        [
            ['Распознавание могил',route('account.decoder.burial.edit')],
            
        ],

        [
            ['Чат',''],
            
        ],
        
    
        [
            ['Оплата',''],

            [
                ['Оплачено',route('account.decoder.payments.paid')],
                ['На проверке',route('account.decoder.payments.verification')],
            ],
        ],


        [
            ['Обучающий материал',''],

            [
                ['Видео',route('account.decoder.training-material.video')],
                ['Документация',route('account.decoder.training-material.file')],
            ],
        ],
    
    ];
}



function mobilePagesAccountAdmin(){
    $seo=[['Настройки',route('account.admin.seo.settings')]];
    
    return [
        
        [
            ['Главная',route('home')],
            
        ],
        
        [
            ['Захоронения',''],

            [
                ['Импортировать',route('account.admin.burial.parser')],
            ]
        ],

    

        [
            ['Кладбища',''],

            [
                ['Импортировать',route('account.admin.parser.cemetery')],
                // ['Добавить кладбища','account.admin.create.cemetery']
            ],
            
        ],
    
         [
            ['Крематории',''],
            
            [
                ['Импортировать',route('account.admin.parser.crematorium')],
                // ['Добавить кладбища','account.admin.create.crematorium']
            ],
            
        ],


        [
            ['Колумбарии',''],
            
            [
                ['Импортировать',route('account.admin.parser.columbarium')],
                // ['Добавить кладбища','account.admin.create.cemetery']
            ],
            
        ],

        [
            ['Организации',''],
            
            [
                ['Импортировать',route('account.admin.parser.organization')],
                // ['Добавить кладбища','account.admin.create.cemetery']
            ],
            
        ], 

        [
            ['SEO',''],
            $seo
        ],
    
    ];
}


function mobilePagesAccountAgency() {

    $organizations=user()->organizations;
    $ul_organizations=[];

    foreach($organizations as $organization){
        $ul_organizations[]=[$organization->title,route('account.agency.organization.settings',$organization->id)];
    }
    $ul_organizations[]=['Добваить организацию',route('account.agency.add.organization')];
    $ul_organizations[]=['Создать организацию',route('account.agency.organization.create-page')];

    return [
        
        [
            ['Организации', ''],
            $ul_organizations,
        ],

        [
            ['Заявки по умершему', ''],

            [
                ['Новые', route('account.agency.organization.aplication.dead.new')],
                ['В работе', route('account.agency.organization.aplication.dead.in-work')],
                ['Завершенные', route('account.agency.organization.aplication.dead.completed')],
            ]
        ],
    
        [
            ['Заявки по поминкам', ''],
            
            [
                ['Новые', route('account.agency.organization.aplication.memorial.new')],
                ['В работе', route('account.agency.organization.aplication.memorial.in-work')],
                ['Завершенные', route('account.agency.organization.aplication.memorial.completed')],
            ]
        ],

        [
            ['Заявки по облогораживанию', ''],
            
            [
                ['Новые', route('account.agency.organization.aplication.beautification.new')],
                ['В работе', route('account.agency.organization.aplication.beautification.in-work')],
                ['Завершенные', route('account.agency.organization.aplication.beautification.completed')],
            ]
        ],


        [
            ['Заявки по ритуальным услугам', ''],
            
            [
                ['Новые', route('account.agency.organization.aplication.funeral-service.new')],
                ['В работе', route('account.agency.organization.aplication.funeral-service.in-work')],
                ['Завершенные', route('account.agency.organization.aplication.funeral-service.completed')],
            ]
        ],

        [
            ['Звонки',''],
           [
                ['Входящие',route('account.agency.organization.calls.stats')]
           ]
        ],

        [
            ['Статистика',''],
            [
                    ['Визиты',route('account.agency.organization.statistics.sessions')]
            ]
        ],


        [
            ['Подчиненные',''],
           [
                ['Список',route('account.agency.users')]
           ]
        ],

        [
            ['Техподдержка',''],
            
            [
                ['Главная',route('account.agency.tickets.index')],
                ['Создать заявку',route('account.agency.tickets.create')],
            ]
        ],

        [
            ['Настройки', route('account.agency.settings')],
        ],

        [
            ['Оплаты',''],
            [
                ['Кошельки',route('account.agency.organization.wallets')],
                //['Зявки',route('account.agency.applications')],
                ['Приориет',route('account.agency.priority.buy')]
            ]
        ],
    

        [
            ['Товар', ''],
            
            [
                ['Создать товар', route('account.agency.add.product')],
                ['Все товары', route('account.agency.products')],
            ]
        ],


        [
            ['Отзывы', ''],
            
            [
                ['Об организации', route('account.agency.reviews.organization')],
                ['О товарах', route('account.agency.reviews.product')],
            ]
        ],

        [
            ['Заказы с маркетплэйса', ''],
            
            [
                ['Новые', route('account.agency.product.orders.new')],
                ['В работе', route('account.agency.product.orders.in-work')],
                ['Завершенные', route('account.agency.product.orders.completed')],
            ]
        ],


        [
            ['Поставщики', ''],
            
            [
                ['Заявки', route('account.agency.provider.requests.products.add')],
                ['Созданные заявки', route('account.agency.provider.requests.products.created')],
                ['Ответы на заявки', route('account.agency.provider.requests.products.answer')],
                ['Избранное', route('account.agency.provider.like.organizations')],
                ['Акции,Скидки', route('account.agency.provider.stocks')],
                ['Создание запроса', route('account.agency.provider.offer.add')],
                ['Создание запроса', route('account.agency.provider.offer.created')],
                ['Ответы на запросы', route('account.agency.provider.offer.answers')],
            ]
        ],
    ];
    
}



function mobilePagesAccountAgent(){
    $pages=[
        
        [
            ['Настройки',route('account.agent.settings')]
        ],


        [
            ['Услуги',route('account.agent.services.index')],
        ],

 
        
    ];
    return $pages;

}

?>