
let rewies_swiper = new Swiper(".rewies_swiper", {
    loop: true,
    spaceBetween: 20,
  
    breakpoints: {
      340: {
        slidesPerView: 1,
      },
      1040: {
        slidesPerView: 2,
      },
     
    },
    navigation: {
      nextEl: ".swiper_button_next_rewies",
      prevEl: ".swiper_button_prev_rewies",
    },
    
});
  

let memorial_dinners_swiper = new Swiper(".memorial_dinners_swiper", {
  spaceBetween: 20,

  breakpoints: {
    340: {
      slidesPerView: 2,
    },
    1040: {
      slidesPerView: 3,
    },
   
  },
  navigation: {
    nextEl: ".swiper_button_next_memorial_dinners",
    prevEl: ".swiper_button_prev_memorial_dinners",
  },
  
});
let memorial_hall_swiper = new Swiper(".memorial_hall_swiper", {
  spaceBetween: 20,

  breakpoints: {
    340: {
      slidesPerView: 2,
    },
    1100: {
      slidesPerView: 3,
    },
    1290: {
      slidesPerView: 4,
    },
   
  },
  navigation: {
    nextEl: ".swiper_button_next_memorial_hall",
    prevEl: ".swiper_button_prev_memorial_hall",
  },
  
});


let news_video_swiper = new Swiper(".news_video_swiper", {
  spaceBetween: 20,

  breakpoints: {
    
    340: {
      slidesPerView: 2,
    },
    1100: {
      slidesPerView: 3,
    },
   
  },
  navigation: {
    nextEl: ".swiper_button_next_video",
    prevEl: ".swiper_button_prev_video",
  },
})


let products_monuments_grave = new Swiper(".products_monuments_grave_swiper", {
  spaceBetween: 20,

  breakpoints: {

    340: {
      slidesPerView: 2,
    },
    1100: {
      slidesPerView: 3,
    },
    1290: {
      slidesPerView: 4,
    },
   
  },
  navigation: {
    nextEl: ".swiper_button_next_products_monuments_grave",
    prevEl: ".swiper_button_prev_products_monuments_grave",
  },
  
});


let products_funeral_service = new Swiper(".products_funeral_service_swiper", {
  spaceBetween: 20,

  breakpoints: {

    340: {
      slidesPerView: 2,
    },
    1100: {
      slidesPerView: 3,
    },
    1290: {
      slidesPerView: 4,
    },
   
  },
  navigation: {
    nextEl: ".swiper_button_next_products_funeral_service",
    prevEl: ".swiper_button_prev_products_funeral_service",
  },
  
});

let reviews_funeral_agencies = new Swiper(".reviews_funeral_agencies_swiper", {
  spaceBetween: 20,
 
  breakpoints: {
    340: {
      slidesPerView: 1,
    },
    1000: {
      slidesPerView: 2,
    },
    1100: {
      slidesPerView: 3,
    },
    1290: {
      slidesPerView: 4,
    },
   
  },
  navigation: {
    nextEl: ".swiper_button_next_reviews_funeral_agencies",
    prevEl: ".swiper_button_prev_reviews_funeral_agencies ",
  },
  
});



let burial_swiper = new Swiper(".burial_swiper", {
  loop: true,
  spaceBetween: 0,
  navigation: {
    nextEl: ".swiper_button_next_rewies",
    prevEl: ".swiper_button_prev_rewies",
  },
  
});

let product_swiper = new Swiper(".product_swiper", {
  loop: true,
  spaceBetween: 0,
  navigation: {
    nextEl: ".swiper_button_next_rewies",
    prevEl: ".swiper_button_prev_rewies",
  },
  
});


var our_products_swiper = new Swiper(".our_products_swiper", {
  breakpoints: {
    840: {
      slidesPerView: 1,
    },
    850: {
      slidesPerView: 2,
    },
    1100: {
      slidesPerView: 3,
    },
   
  },
  spaceBetween: 30,
  autoplay: {
    delay: 2500,
    disableOnInteraction: false,
  },
});



var organizations_swiper = new Swiper(".organizations_swiper", {
  breakpoints: {
    840: {
      slidesPerView: 1,
    },
    850: {
      slidesPerView: 2,
    },
    1100: {
      slidesPerView: 3,
    },
   
  },
  spaceBetween: 30,
  autoplay: {
    delay: 2500,
    disableOnInteraction: false,
  },
});


sale_products_swiper= new Swiper(".sale_products_swiper", {
  breakpoints: {
    340: {
      slidesPerView: 1,
    },
    650: {
      slidesPerView: 2,
    },
    700: {
      slidesPerView: 3,
    },
   
  },
  spaceBetween: 30,
  autoplay: {
    delay: 1500,
    disableOnInteraction: false,
  },
});

galerey_swiper= new Swiper(".galerey_swiper", {
  breakpoints: {
    340: {
      slidesPerView: 1,
    },
    650: {
      slidesPerView: 2,
    },
    700: {
      slidesPerView: 3,
    },
   
  },
  navigation: {
    nextEl: ".swiper-button-next-galerey",
    prevEl: ".swiper-button-prev-galerey",
  },
  pagination: {
    el: ".swiper_pagination_galerey",
  },
  spaceBetween: 30,
  autoplay: {
    delay: 1500,
    disableOnInteraction: false,
  },
});



stock_products_organization= new Swiper(".stock_products_organization", {
  breakpoints: {
    340: {
      slidesPerView: 1,
    },
    650: {
      slidesPerView: 2,
    },
    700: {
      slidesPerView: 3,
    },
   
  },
  spaceBetween: 30,
  autoplay: {
    delay: 1500,
    disableOnInteraction: false,
  },
});


$( ".li_faq .flex_li_service" ).on( "click", function() {
  if($(this).attr('page')!='marketplace'){
    $(this).children('.open_faq').toggleClass('open_faq_active')
    $(this).siblings('.text_li').slideToggle()
  }
 
})

$( ".open_children_pages" ).on( "click", function() {
  $(this).children('.children_pages').slideToggle()
})


$('.city_selected').on( "click", function() {
  $('#city_form').modal('show')
});


$( ".li_label_block" ).on( "click", function() {
  $( ".li_label_block" ).removeClass('active_label_product')
  $(this).addClass('active_label_product')

  let id_label=$(this).attr('id_label')

  $( ".content_single_product" ).each(function() {
    if($( this ).attr('id_block')==id_label){
      $( this ).addClass('content_single_product_active')
    }else{
      $( this ).removeClass('content_single_product_active')
    }
  });
})

$( ".close_message" ).on( "click", function() {
  $('.bac_black').fadeOut()
})


let inputs = document.querySelectorAll('.input__file_2');
Array.prototype.forEach.call(inputs, function (input) {
  let label = input.nextElementSibling,
    labelVal = label.querySelector('.input__file-button-text_2').innerText;

  input.addEventListener('change', function (e) {
    let countFiles = '';
    if (this.files && this.files.length >= 1)
      countFiles = this.files.length;

    if (countFiles)
      if(countFiles==1){
        label.querySelector('.input__file-button-text_2').innerText = 'Файл выбран';
      }else{
        label.querySelector('.input__file-button-text_2').innerText = countFiles+ ' Файла выбрано';
      }
    else
      label.querySelector('.input__file-button-text_2').innerText = labelVal;
  });
});







$( ".form_services_add input" ).on( "click", function() {
    var allVals = [];
    $('.form_services_add input:checked').each(function() {
      allVals.push($(this).attr('price'));
    });
    $('.flex_form_services_add .title_middle p').html(allVals.reduce((partialSum, a) => partialSum + Number(a), 0));
})

$( ".block_services_order .title_label" ).on( "click", function() {
  $(this).siblings('.ul_services_order').slideToggle(200);
  $(this).children('img').toggleClass('arrow_active')
})



$( ".copy_adres" ).on( "click", function() {
	var inputc = document.body.appendChild(document.createElement("input"));
	inputc.value = $(this).attr('adres');
	inputc.focus();
	inputc.select();
	document.execCommand('copy');
	inputc.parentNode.removeChild(inputc);
	alert("Ссылка скопирована");
})



$( "#open_words_memory" ).on( "click", function() {
  $( ".content_single_product" ).removeClass('content_single_product_active')
  $( ".li_label_block" ).removeClass('active_label_product')
  $( ".content_single_product" ).each(function() {
    if($( this ).attr('id_block')==3){
      $( this ).addClass('content_single_product_active')
    }
  });
  $( ".li_label_block" ).each(function() {
    if($( this ).attr('id_label')==3){
      $( this ).addClass('active_label_product')
    }
  });
});

$( "#similar_burials" ).on( "click", function() {
  $( ".content_single_product" ).removeClass('content_single_product_active')
  $( ".li_label_block" ).removeClass('active_label_product')
  $( ".content_single_product" ).each(function() {
    if($( this ).attr('id_block')==5){
      $( this ).addClass('content_single_product_active')
    }
  });
  $( ".li_label_block" ).each(function() {
    if($( this ).attr('id_label')==5){
      $( this ).addClass('active_label_product')
    }
  });
});

$( ".open_form_print" ).on( "click", function() {
  $('.input_print_form').css('display','flex');
})

$( ".open_rent_object" ).on( "click", function() {
  $('#order_id_input').val($(this).attr('order_id'));
  $('.input_print_form').css('display','flex');
})


$( ".open_personal_image_form" ).on( "click", function() {
  $('#image_personal').css('display','flex');
})

$( ".open_monument_image_form" ).on( "click", function() {
  $('#image_monument').css('display','flex');
})




$( ".edge_li" ).on( "click", function() {
  block_parent=$( this ).parent('.ul_location')
  id=$( this ).attr('id_edge')
  $( ".cities_ul" ).each(function() {
    if(id==$(this).attr('id_edge_ul')){
      $(block_parent).css('display','block')
      $( ".edge_li" ).hide()
      $(this).css('display','grid')
      $('.block_location .title_news').html('Город')
    }
  });
})


$( ".city_li" ).on( "click", function() {
  block_parent=$( this ).parent('.ul_location')
  id=$( this ).attr('id_city')
  $( ".cemetery_ul" ).each(function() {
    if(id==$(this).attr('id_city_ul')){
      $(block_parent).css('display','block')

      $( ".city_li" ).hide()
      $(this).css('display','grid')
      $('.block_location .title_news').html('Кладбище')
    }
  });
})


$( ".li_cemetery_2" ).on( "click", function() {
  value_id=$(this).attr('id_cemetery')
  value_html=$(this).html()

  $('input[name="id_cemetery"]').val(value_id)
  $('input[name="location"]').val(value_html)

  $('#beautification_form').modal('show');
  

});


$( ".li_cemetery_3" ).on( "click", function() {
  value_id=$(this).attr('id_cemetery')
  value_html=$(this).html()

  $('input[name="id_cemetery"]').val(value_id)
  $('input[name="location"]').val(value_html)  
  $('#location_form_2').modal('hide')

});

$( ".title_page_sidebar" ).on( "click", function() {
 $(this).siblings('.pages_children_sidebar').slideToggle();
 $(this).children('.open_children_pages_sidebar').toggleClass('active_open_children_pages_sidebar')

})



$( ".delete_cemetery" ).on( "click", function() {
    $(this).parent('.li_cemetery_agent').remove()
})

$( ".more_children_cats_product" ).on( "click", function() {
  $(this).addClass('active_more_children_cats_product')
  $(this).siblings('.ul_children_cat_product').children('.li_cat_children_product').show()
})



$( ".see_all_cats_product" ).on( "click", function() {
  $('.li_cat_product').show()
})

$( ".li_cat_main_marketplace" ).on( "click", function() {
  $(this).siblings('.ul_childern_cats_marketplace').slideToggle();
  $(this).children('.open_children_cats_icon').toggleClass('active_icon_open_children_cats')
})

$('.active_category').parent().siblings('.li_cat_main_marketplace').addClass('active_main_category')



$('.btn_play_video').click(function() {
  $(this).hide()
  var video = $(this).siblings('video').get(0);
  video.paused ? video.play() : video.pause();
});

$('video').click(function() {
  if($(this).play){
      $(this).pause()
      $('.btn_play_video').show()
    }
})


$('.block_filter_cemeteries select').on( "change", function() {
    id_cemetery=$(this).children('option:checked').val()
    if(id_cemetery=='Кладбища'){
      $( ".li_order" ).show()
    }else{
      $( ".li_order" ).each(function() {
        if($(this).attr('id_cemetery')!=id_cemetery){
          $(this).hide()
        }else{
          $(this).show()
        }
      })
    }
    
})



// $( ".product_single .input_additional" ).on( "click", function() {
//   count=$( ".product_single .count_product_single input").val()
//   let allVals = [Number($('.flex_main_price .title_middle span').attr('price'))];
//   $('.product_single .input_additional input:checked').each(function() {
//     allVals.push($(this).attr('price'));
//   });
//   $('.flex_main_price .title_middle span').html((allVals.reduce((partialSum, a) => partialSum + Number(a), 0))*count);
// })

$( ".product_single .count_product_single input").on( "input", function() {
  count=$(this).val()
  let allVals = [Number($('.flex_main_price .title_middle span').attr('price'))];
  $('.product_single .input_additional input:checked').each(function() {
    allVals.push($(this).attr('price'));
  });
  $('.flex_main_price .title_middle span').html((allVals.reduce((partialSum, a) => partialSum + Number(a), 0))*count);


})



$('#funeral_services_form select[name="funeral_service"]').on( "change", function() {
  service=$(this).children('option:checked').val()
  if(service=='1'){
    $('.label_city').html('Город отправки')
    $( ".service_cargo_200" ).css('display','flex')
    $('.service_funeral_arrangements').hide()
  }
  if(service=='2'){
    $('.label_city').html('Выберите город')
    $('.service_funeral_arrangements').hide()
    $( ".service_cargo_200" ).hide()
  }
  if(service=='3'){
    $('.label_city').html('Выберите город')
    $('.service_funeral_arrangements').css('display','flex')
    $( ".service_cargo_200" ).hide()
  }
  
})

$( ".checkbox input" ).on( "change", function() {
  $(this).parent('.checkbox').toggleClass('active_checkbox')
})


$( ".open_call_time" ).on( "click", function() {
  $(this).siblings('.call_time').toggle()
})



$( ".open_funeral_arrangements" ).on( "click", function() {
  $( '#funeral_services_form select[name="funeral_service"] option' ).each(function() {
    if($(this).val()==3){
      $(this).prop('selected', true);
    }
  })
  $('#funeral_services_form').modal('show')
  $('.label_city').html('Выберите город')
  $('.service_funeral_arrangements').css('display','flex')
  $( ".service_cargo_200" ).hide()
})


$( ".open_shipping_200" ).on( "click", function() {
  $( '#funeral_services_form select[name="funeral_service"] option' ).each(function() {
    if($(this).val()==1){
      $(this).prop('selected', true);
    }
  })
  $('#funeral_services_form').modal('show')
  $('.label_city').html('Город отправки')
  $( ".service_cargo_200" ).css('display','flex')
  $('.service_funeral_arrangements').hide()
})

$( ".open_organization_cremation" ).on( "click", function() {
  $( '#funeral_services_form select[name="funeral_service"] option' ).each(function() {
    if($(this).val()==2){
      $(this).prop('selected', true);
    }
  })
  $('#funeral_services_form').modal('show')
  $('.label_city').html('Выберите город')
  $('.service_funeral_arrangements').hide()
  $( ".service_cargo_200" ).hide()
})



$( ".open_all_content_block" ).on( "click", function() {
  $(this).parent().hide()
  $(this).parent().siblings('.content_all').show()
  
})



$( ".menu_single_organization" ).on( "click", function() {
  
  $('.menu_single_organization').removeClass('menu_single_organization_active')
  $(this).addClass('menu_single_organization_active')
  let id_block=$(this).attr('id_block')
  $( ".flex_block_single_organization" ).each(function() {
    if($(this).attr('id_block')==id_block){
      $(this).addClass('flex_block_single_organization_active')
    }else{
      $(this).removeClass('flex_block_single_organization_active')
    }
  })

})

$( ".open_all_reviews_organization" ).on( "click", function() {
  $('.flex_block_single_organization').removeClass('flex_block_single_organization_active')
  $('#block_reviews').addClass('flex_block_single_organization_active')
  $('.menu_single_organization').removeClass('menu_single_organization_active')
  $('#title_block_2').addClass('menu_single_organization_active')
})


$( ".icon_btn_single_organization" ).on( "mouseover", function() {
  $(this).children('.blue_icon').hide()
  $(this).children('.white_icon').show()
} );
$( ".icon_btn_single_organization" ).on( "mouseout", function() {
  $(this).children('.white_icon').hide()
  $(this).children('.blue_icon').show()
} );





$(".to_top").click(function() {
    $([document.documentElement, document.body]).animate({
        scrollTop: $("body").offset().top
    }, 80);
});

$(".open_big_header").click(function() {
  $('.header_big').toggleClass('active_big_header')
});


$(".filter_sort").click(function() {
  $(this).children('.ul_sort').toggleClass('active_ul_sort')
})



function timeWork(time_start,time_end){
  // Получаем текущее время на устройстве пользователя
  const currentTime = new Date();

  // Получаем компоненты времени
  const hours = String(currentTime.getHours()).padStart(2, '0');
  const minutes = String(currentTime.getMinutes()).padStart(2, '0');
  const seconds = String(currentTime.getSeconds()).padStart(2, '0');
  const time_now = `${hours}:${minutes}`;

  
  if(time_now>time_start){
    return 'Окрыто';

  }
  return 'Закрыто';

}



function showWelcomeMessage() {
  // Проверяем, есть ли уже установленный флаг
  if (!sessionStorage.getItem('hasVisited')) {
      // Если нет, показываем уведомление
      $('.city_question').css('display','flex');

      // Устанавливаем флаг, что пользователь уже посещал сайт в этой сессии
      sessionStorage.setItem('hasVisited', 'true');
  }
}
// Вызываем функцию при загрузке страницы
window.onload = showWelcomeMessage;

$(".open_choose_city").click(function() {
  $('#city_form').modal('show')
});


$(".img_burial_edit_decoder .blue_btn").click(function() {
  $('.comments_burial').toggle()
});

$(".comment_burial").click(function() {
  $('#comment_burial').val($(this).html())
  $('#comment_burial').parent('form').submit();
});



$('.li_children_page_sidebar_active').parent('.pages_children_sidebar').css('display','block')
$('.li_children_page_sidebar_active').parent('.pages_children_sidebar').siblings('.title_page_sidebar').children('.open_children_pages_sidebar').addClass('active_open_children_pages_sidebar')


$(".open_ul_agency_organizations").click(function() {
  $('.ul_agency_organizations').slideToggle()
});

let addZoom = target => {
  // (A) GET CONTAINER + IMAGE SOURCE
  let container = document.getElementById(target),
      imgsrc = container.currentStyle || window.getComputedStyle(container, false);
      imgsrc = imgsrc.backgroundImage.slice(4, -1).replace(/"/g, "");
 
  // (B) LOAD IMAGE + ATTACH ZOOM
  let img = new Image();
  img.src = imgsrc;
  img.onload = () => {
    // (B1) CALCULATE ZOOM RATIO
    let ratio = img.naturalHeight / img.naturalWidth,
        percentage = ratio * 100 + "%";
 
    // (B2) ATTACH ZOOM ON MOUSE MOVE
    container.onmousemove = e => {
      let rect = e.target.getBoundingClientRect(),
          xPos = e.clientX - rect.left,
          yPos = e.clientY - rect.top,
          xPercent = xPos / (container.clientWidth / 100) + "%",
          yPercent = yPos / ((container.clientWidth * ratio) / 100) + "%";
 
      Object.assign(container.style, {
        backgroundPosition: xPercent + " " + yPercent,
        backgroundSize:1000 + "px"
      });
    };
 
    // (B3) RESET ZOOM ON MOUSE LEAVE
    container.onmouseleave = e => {
      Object.assign(container.style, {
        backgroundPosition: "center",
        backgroundSize: "cover"
      });
    };
  }
};


$(".open_working_times").click(function() {
  $('.ul_working_days').slideToggle(200)
  $(this).toggleClass('rotate_arrow_open')
});


$("button[type='submit']").click(function() {
  $( ".input_time_now" ).each(function() {
    $(this).val(new Date())
  })
})

// $( ".time_end_aplication" ).each(function() {
//   // Время, до которого нужно отсчитывать (в формате "HH:MM")
//   const targetTime = $(this).html();

//   // Функция для запуска таймера
//   function startTimer(targetTime) {
//       // Получаем текущее время
//       const now = new Date();

//       // Разбираем целевое время
//       const [hours, minutes] = targetTime.split(':').map(Number);

//       // Устанавливаем целевое время
//       const targetDate = new Date();
//       targetDate.setHours(hours, minutes, 0, 0);

//       // Если целевое время уже прошло, добавляем один день
//       if (targetDate <= now) {
//           targetDate.setDate(targetDate.getDate() + 1);
//       }

//       // Функция для обновления таймера
//       function updateTimer() {
//           const now = new Date();
//           const timeDifference = targetDate - now;

//           // Если время истекло
//           if (timeDifference <= 0) {
//               clearInterval(interval);
//               $(this).html('Время истекло!');
//               return;
//           }

//           // Вычисляем часы, минуты и секунды
//           const hoursLeft = Math.floor(timeDifference / (1000 * 60 * 60));
//           const minutesLeft = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
//           const secondsLeft = Math.floor((timeDifference % (1000 * 60)) / 1000);

//           // Форматируем время в "HH:MM:SS"
//           const formattedTime = `${String(hoursLeft).padStart(2, '0')}:${String(minutesLeft).padStart(2, '0')}:${String(secondsLeft).padStart(2, '0')}`;

//           // Обновляем текст таймера
//           console.log(formattedTime)
//           $(this).html(formattedTime);
//       }

//       // Обновляем таймер каждую секунду
//       const interval = setInterval(updateTimer, 1000);

//       // Запускаем таймер сразу
//       updateTimer();
//   }

//   // Запускаем таймер
//   startTimer(targetTime);
// });




$(".open_reason_failure_btn").click(function() {
  $(this).children('img').toggleClass('rotate_arrow_open')
  $(this).siblings('.text_black').slideToggle(200)
});


$(".phone").mask("+7 (999) 999-9999");



$(".open_mobile_header").click(function() {
  $('.mobile_header').toggleClass('mobile_header_active')
});


$(".open_children_pages").click(function() {
 $(this).siblings('.children_pages_mobile_header').toggleClass('children_pages_mobile_header_active')
});

