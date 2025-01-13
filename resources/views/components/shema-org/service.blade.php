<script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "Service",
      "name": "{{$service->title}}",
      "description": "{{$service->content}}",
      "offers": {
        "@type": "Offer",
        "price": "{{$service->price}}",
        "priceCurrency": "RUB"
      }
    }
</script>
