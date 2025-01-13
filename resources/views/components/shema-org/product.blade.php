<script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@id": "{{$product->route()}}",
      "@type": "Product",
      "name": "{{$product->title}}",
      "image":"{{ asset('storage/uploads_product/'.$product->getImages[0]->title) }}",
      "description": "{{$product->content}}",
      "brand": {
        "@type": "Brand",
        "name": "{{$product->organization->title}}"
      },
      "offers": {
        "@type": "Offer",
        "price": "{{ priceProduct($product) }}",
        "priceCurrency": "RUB",
        "availability": "https://schema.org/InStock",
        "url": "{{$product->route()}}"
      }
    }
</script>