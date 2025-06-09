<?php 

if(isset($cemetery)){
    $model=$cemetery;
}
if(isset($columbarium)){
    $model=$columbarium;
}
if(isset($crematorium)){
    $model=$crematorium;
}
if(isset($mortuary)){
    $model=$mortuary;
}
if(isset($object)){
    $model=$object;
}
?>

<script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@id": "{{$model->route()}}",
      "@type": "Place",
      "name": "{{$model->title}}",
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": "{{$model->width}}",
        "longitude": "{{$model->longitude}}"
      },
      "openingHours": "{{workingDaysForShema($model->ulWorkingDaysForShema())}}",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "{{$model->adres}}",
        "addressLocality": "{{$model->city->title}}",
        "addressCountry": "RU"
      }
    }
</script>