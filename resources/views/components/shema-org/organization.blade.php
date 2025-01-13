<script type="application/ld+json">

    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "{{$organization->title}}",
      "image": "{{$organization->urlImg()}}",
      "telephone": "{{$organization->phone}}",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "{{$organization->adres}}",
        "addressLocality": "{{$organization->city->title}}",
        "addressCountry": "RU"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": "{{$organization->width}}",
        "longitude": "{{$organization->longitude}}"
      },
      "openingHours": "Mo-Fr 09:00-18:00",
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "{{$organization->rating}}",
        @if($organization->countReviews()>0)
          "reviewCount": "{{$organization->countReviews()}}"
        @endif
      },


      "review": [
        @foreach ($organization->reviews as $review)
            {
                "@type": "Review",
                "author": "{{$review->name}}",
                "datePublished": "{{$review->created_at->format('Y-m-d')}}",
                "reviewBody": "{{$review->content}}",
                "reviewRating": {
                "@type": "Rating",
                "ratingValue": "{{$review->rating}}"
                }
            }   
            @if (!$loop->last)
                ,
            @endif
        @endforeach
      ]
}
</script>

