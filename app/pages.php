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
    $organizations=user()->organizations;
    $ul_organizations=[];
    foreach($organizations as $organization){
        $ul_organizations[]=[$organization->title,'account.agency.organization.settings',$organization->id];
    }
    $ul_organizations[]=['Привязать организацию','account.agency.add.organization'];
    $ul_organizations[]=['Создать организацию','account.agency.organization.create-page'];

       $pages=[
        ['Организации','storage/uploads/Icon_sidebar_2.svg',
            $ul_organizations
        ],

        ['Заявки по умершему','storage/uploads/Vector (17).svg',
            [
                ['Новые','account.agency.organization.aplication.dead.new'],  
                ['В работе','account.agency.organization.aplication.dead.in-work'],                
                ['Завершенные','account.agency.organization.aplication.dead.completed'],                
                // ['Незавершенные','account.agency.organization.aplication.dead.not-completed'],                
            ]
        ],

        ['Заявки по поминкам','storage/uploads/Vector (17).svg',
            [
                ['Новые','account.agency.organization.aplication.memorial.new'], 
                ['В работе','account.agency.organization.aplication.memorial.in-work'],
                ['Завершенные','account.agency.organization.aplication.memorial.completed'],                
                ['Незавершенные','account.agency.organization.aplication.memorial.not-completed'],                
            ]
        ],


        ['Заявки по облогораживанию','storage/uploads/Vector (17).svg',
            [
                ['Новые','account.agency.organization.aplication.beautification.new'],                
                ['В работе','account.agency.organization.aplication.beautification.in-work'],  
                ['Завершенные','account.agency.organization.aplication.beautification.completed'],         
                ['Незавершенные','account.agency.organization.aplication.beautification.not-completed'],                
       

                              
            ]
        ],

        ['Заявки по ритуальным услугам','storage/uploads/Vector (17).svg',
            [
                ['Новые','account.agency.organization.aplication.funeral-service.new'],    
                ['В работе','account.agency.organization.aplication.funeral-service.in-work'], 
                ['Завершенные','account.agency.organization.aplication.funeral-service.completed'],                
                ['Незавершенные','account.agency.organization.aplication.funeral-service.not-completed'],                
            ]
        ],

        
        ['Настройки','storage/uploads/icon_sidebar.svg',
           [
            ['Настройки','account.agency.settings']
           ]
        ],


        ['Оплаты','storage/uploads/Icon_pay_aplication.svg',
            [
                ['Заявки','account.agency.applications']
            ]
        ],


        ['Товар','storage/uploads/Icon_sidebar_2.svg',
            [
                ['Создать товар','account.agency.add.product'],
                ['Все товары','account.agency.products'],
            ]
        ],


        ['Отзывы','storage/uploads/Icon_sidebar_2.svg',
            [
                ['Об организации','account.agency.reviews.organization'],
                ['О товарах','account.agency.reviews.product'],
                
            ]
        ],

        ['Заказы с маркетплэйса','storage/uploads/Icon_sidebar_2.svg',
            [
                ['Новые','account.agency.product.orders.new'],            
                ['В работе','account.agency.product.orders.in-work'],                
                ['Завершенные','account.agency.product.orders.completed'],                
            ]
        ],
       


        ['Поставщики','storage/uploads/Icon_sidebar_2.svg',
            [
                ['Заявки','account.agency.provider.requests.products.add'],
                ['Созданные заявки','account.agency.provider.requests.products.created'],
                ['Ответы на заявки','account.agency.provider.requests.products.answer'],
                ['Избранное','account.agency.provider.like.organizations'],
                ['Акции','account.agency.provider.stocks'],
                ['Скидки','account.agency.provider.discounts'],
                ['Создание запроса','account.agency.provider.offer.add'],
                ['Созданные запросы','account.agency.provider.offer.created'],
                ['Ответы на запросы','account.agency.provider.offer.answers'],  
            ]
        ],
        

        
       
    ];
    return $pages;
}




function mobilePages(){
    
   $catalog_organizations = [['Ритуальные агенства', route('organizations')],['Ритуальные товары,услуги', route('pricelist')]];

    // if (Auth::user() && (user()->role == 'organization' || user()->role == 'organization-provider' || user()->role == 'admin') && versionProject()) {
    //     $catalog_organizations[] = ['Каталог поставщиков', route('organizations.provider')];
    // }

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
        
        // Блок "Облогораживание" - показываем только если versionProject() != true
        (!versionProject() ? [
            ['Облогораживание', ''],
            [
                ['Товары и услуги', route('pricelist')],
                ['Маркетплэйс', route('marketplace')],
            ],
        ] : null),
        
        (!versionProject() ? [
            ['Оформление заказа', ''],
            [
                ['Захоронений', route('checkout.burial')],
                ['Услуг', route('checkout.service')],
            ],
        ] : null),
        
        [
            ['Ритуальные обьекты', ''],
            [
                ['Кладбища', route('cemeteries')],
                ['Морги', route('mortuaries')],
                ['Колумбарии', route('columbariums')],
                ['Крематории', route('crematoriums')],
                ['Церкви', route('churches')],
                ['Мечети', route('mosques')],
            ],
        ],
        
        [
            ['Ритуальные услуги', ''],
            $catalog_organizations,
        ],
        
        // Блок "Информация" - изменяем в зависимости от versionProject()
        [
            ['Информация', ''],
            array_filter([
                (!versionProject() ? ['Наши работы', route('our.products')] : null),
                (!versionProject() ? ['Статьи', route('news')] : null),
                ['Контакты', route('contacts')],
            ]),
        ],
        
    ];

    // Удаляем null-элементы, которые могли появиться из-за условных блоков
    $pages = array_filter($pages);
    
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
                ['Список',route('account.admin.burial')],
                ['Импортировать',route('account.admin.burial.parser')],
            ]
        ],

    

        [
            ['Кладбища',''],

            [
                ['Список',route('account.admin.cemetery')],
                ['Импортировать',route('account.admin.parser.cemetery')],
                // ['Добавить кладбища','account.admin.create.cemetery']
            ],
            
        ],
    
         [
            ['Крематории',''],
            
            [
                ['Список',route('account.admin.crematorium')],
                ['Импортировать',route('account.admin.parser.crematorium')],
                // ['Добавить кладбища','account.admin.create.crematorium']
            ],
            
        ],


        [
            ['Колумбарии',''],
            
            [
                ['Список',route('account.admin.columbarium')],
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


function mobilePagesAccountAgecny() {

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
            ['Настройки', route('account.agency.settings')],
        ],

        [
            ['Оплаты', route('account.agency.applications')],
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