<footer class="mt-3">
    <div class="container">
        <div class="footer_block">
            <div class="block_top">
                <div class="menu_group w-100">
                    <ul>
                        @guest
                        <li><a href="{{ route('login') }}" class="active">Войти в систему</a></li>
                        @endguest
                    </ul>
                    <div class="group_menu d-flex gap-3 gap-sm-5">
                        <ul class="d-flex flex-column" style="white-space:nowrap;">
                            <li><a href="/about">Об Ассоциации</a></li>
                            <li><a href="{{ route('courses-v2.index') }}">Курсы</a></li>
                            <li><a href="{{ route('v2.video.index') }}">Видеотека</a></li>
                        </ul>
                        <ul class="d-flex flex-column" style="white-space:nowrap;">
                            <li><a href="{{ route('v2.meetings.index') }}">Наши встречи</a></li>
                            <li><a href="{{ route('v2.club.index') }}">Онлайн-клубы</a></li>
                        </ul>
                    </div>
                </div>
                <div class="social_nets" style="margin-left: 30px">
                    <a target="_blank" href="https://vk.com/associacia_chpp"><img src="/img/social1.svg" alt=""></a>
                    <a target="_blank" href="https://t.me/+WqnwojGKWjJkNDAy"><img src="/img/social2.svg" alt=""></a>
                </div>
            </div>
            <div class="block_bottom ">
                <div class="text_left">
                    <span>Мы используем cookie-файлы, оставаясь на сайте, вы подтверждаете свое согласие на их использование.</span>
                    <span style="font-size: 10px">© 2024 appp-psy.ru — АЧПП.</span>
                </div>
                <div class="right_text" style="text-align: left">
                    <a download href="https://docs.google.com/document/d/1-PJK9iC1tzglEyfocDZmebhms4KrxnAc/edit?usp=drivesdk&ouid=101505722311388968680&rtpof=true&sd=true">Этический кодекс</a>
                    <a download href="https://docs.google.com/document/d/1-IDLjXzgERzZXfYymEm8JSvay1lEEZQC/edit?usp=drivesdk&ouid=101505722311388968680&rtpof=true&sd=true">Доставка и Возврат</a>
                    <a href="https://docs.google.com/document/d/1-P3o9mBsLNFtCowS6M5eTYobsJJ8sXiU/edit?usp=drivesdk&ouid=101505722311388968680&rtpof=true&sd=true" download>Публичная оферта АЧПП</a>
                    <a download href="https://docs.google.com/document/d/1-Jj8cKFgcIxwmKkAm5l8zPA-Gj8P2MW0/edit?usp=drivesdk&ouid=101505722311388968680&rtpof=true&sd=true">Политика&nbsp;обработки&nbsp;персональных&nbsp;данных</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
   m[i].l=1*new Date();
   for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
   k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

   ym(103378257, "init", {
        clickmap:true,
        trackLinks:true,
        accurateTrackBounce:true,
        webvisor:true
   });
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/103378257" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
