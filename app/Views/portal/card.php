<article class="news-card">
    <a class="card-image" href="<?= e(url('noticia/' . $item['slug'])) ?>" tabindex="-1" aria-hidden="true"><?php articleImage($item); ?></a>
    <div class="card-body"><a class="category-label" href="<?= e(url('categoria/' . $item['category_slug'])) ?>"><?= e($item['category']) ?></a><h3><a href="<?= e(url('noticia/' . $item['slug'])) ?>"><?= highlighted($item['title'], $q ?? '') ?></a></h3><p><?= e($item['excerpt']) ?></p><div class="meta"><span><?= e($item['author']) ?></span><time datetime="<?= e(date(DATE_ATOM, strtotime($item['published_at']))) ?>"><?= e(date('d/m/Y', strtotime($item['published_at']))) ?></time></div></div>
</article>
