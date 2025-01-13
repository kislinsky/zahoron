<?php $burial=$product;?>
<script type="application/ld+json">
    {
      "@context": "http://schema.org",
      "@type": "Person",
      "name": "{{$burial->surname}} {{$burial->name}} {{$burial->patronymic}}",
      "birthDate": "{{dateNewFormat($burial->date_birth)}}",
      "deathDate": "{{dateNewFormat($burial->date_death)}}",
      "description": "Захоронение на кладбище '{{$burial->cemetery->title}}'.",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "{{$burial->cemetery->city->title}}",
        "addressCountry": "RU"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": "{{$burial->width}}",
        "longitude": "{{$burial->longitude}}"
      },
      "affiliation": [
        {
          "@type": "Cemetery",
          "name": "Кладбище '{{$burial->cemetery->title}}'",
          "address": {
            "@type": "PostalAddress",
            "streetAddress": "{{$burial->cemetery->adres}}",
            "addressLocality": "{{$burial->cemetery->city->title}}",
            "addressCountry": "RU"
          },
          "geo": {
            "@type": "GeoCoordinates",
            "latitude": "{{$burial->width}}",
            "longitude": "{{$burial->longitude}}"
          }
        }
        
      ]
    }
    </script>