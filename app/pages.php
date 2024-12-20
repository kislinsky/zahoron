<?php

function adminPages(){
    $pages=[
        ['Кладбища','storage/uploads/mdi_grave-stone (1).svg',
            [
                ['Список','account.admin.cemetery'],
                ['Импортировать','account.admin.parser.cemetery'],
                // ['Добавить кладбища','account.admin.create.cemetery']
            ],
        ],
        ['Морги','storage/uploads/game-icons_morgue-feet (2).svg',
            [
                ['Список','account.admin.mortuary'],
                ['Импортировать','account.admin.parser.mortuary'],
                // ['Добавить кладбища','account.admin.create.cemetery']
            ],
        ],
        ['Крематории','storage/uploads/emojione-monotone_funeral-urn.svg',
            [
                ['Список','account.admin.crematorium'],
                ['Импортировать','account.admin.parser.crematorium'],
                // ['Добавить кладбища','account.admin.create.cemetery']
            ],
        ],
        ['Колумбарии','storage/uploads/mdi_grave-stone (2).svg',
            [
                ['Список','account.admin.columbarium'],
                ['Импортировать','account.admin.parser.columbarium'],
                // ['Добавить кладбища','account.admin.create.cemetery']
            ],
        ],

        ['Организации','storage/uploads/mdi_grave-stone (2).svg',
            [
                ['Импортировать','account.admin.parser.organization'],
                // ['Добавить кладбища','account.admin.create.cemetery']
            ],
        ]

        
       
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



function organizationPages(){
    $organizations=user()->organizations;
    $ul_organizations=[];
    foreach($organizations as $organization){
        $ul_organizations[]=[$organization->title,'account.agency.organization.settings',$organization->id];
    }
    $ul_organizations[]=['Добваить организацию','account.agency.add.organization'];
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


?>