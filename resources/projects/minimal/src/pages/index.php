<?php
layout("default");

?>
<article>
  <h1>hello</h1>

  <p>to the world</p>

  <?= $image_tag("src/pages/kitty.jpg", "600x", ['alt' => 'this is the cat']) ?>

  <caption>Foto von <a href="https://unsplash.com/de/@yerlinmatu?utm_content=creditCopyText&utm_medium=referral&utm_source=unsplash">Yerlin Matu</a>
    auf <a href="https://unsplash.com/de/fotos/flachfokusfotografie-von-weissen-und-braunen-katzen-GtwiBmtJvaU?utm_content=creditCopyText&utm_medium=referral&utm_source=unsplash">Unsplash</a>
  </caption>
</article>
