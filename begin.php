<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>まごころパン屋さん紹介サイト</title>
    
    <style>
        :root {
            --white: #ffffff;
        }

        body {
            margin: 0;
            padding: 0;
            /*
            background-color: #fcf6ed;
            */
            
        }

        
        .fade-slider {
            position: relative;
            width: 100%;
            height: 90vh;
            overflow: hidden;
        }

        
        .fade-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1.2s ease-in-out; 
        }

       
        .fade-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        
        .slide-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: var(--white);
            font-size: 2rem;
            font-weight: bold;
            text-shadow: 2px 2px 10px rgba(0,0,0,0.5);
            text-align: center;
        }

        
        .fade-slide.active {
            opacity: 1; /* 不透明にする（見えるようになる） */
        }
        
        .text-container {
  	   text-align: center;
}
	.nav-banner {
    background-color: #fce2c4; /* 写真のような薄いグレー */
    padding: 10px 0;
    width: 100%;
    
    
    position: sticky;    
    top: 0;              /* 画面の一番上にくっつける */
    z-index: 1000;       /* 画像やスライダーの下にメニューが隠れないように手前に出す */
    /* 👆 ここまで追加 */
}

/* メニューの箇条書きの点を消し、横並び（Flexbox）にする */
.nav-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center; /* 中央寄せ */
    align-items: center;     /* 上下中央揃え */
    gap: 30px;              /* 項目同士の間隔 */
}

/* リンク全体のデザイン調整 */
.nav-list li a {
    text-decoration: none;  /* リンクの下線を消す */
    color: #000000;       
    display: flex;
    flex-direction: column; /* 英語と日本語を縦に並べる */
    align-items: center;
    text-align: center;
}

/* 英語部分のスタイル */
.nav-list .en {
    font-size: 1.2rem;
    font-family: "游ゴシック体", "Yu Gothic", sans-serif;
    font-weight: bold;
    letter-spacing: 0.05em; /* 文字の間隔を少し広げる */
}

/* 日本語部分のスタイル */
.nav-list .ja {
    font-size: 0.75rem;
    font-family: "游ゴシック体", "Yu Gothic", sans-serif;
    margin-top: 4px;
}

/* ロゴ画像のサイズ調整 */
.nav-logo img {
    height: 70px;           /* 高さを指定してバランスを取る */
    width: auto;
}

/* マウスを載せたとき（タップしたとき）に少し透明にするエフェクト */
.nav-list li a:hover {
    opacity: 0.7;
    transition: opacity 0.3s;
}

.shop-info-container {
    display: flex;
    align-items: center; /* 上下の中央揃え */
    gap: 40px;           /* 画像と文字の間のすき間 */
    max-width: 800px;    /* 全体の最大幅（お好みで調整してください） */
    margin: 20px auto;   /* 画面中央に寄せる */
    padding: 0 20px;     /* 端の余白 */
}

/* 店舗の文字情報のスタイル調整 */
.shop-details p {
    font-size: 1rem;
    line-height: 1.8;   /* 行間を少し広げて読みやすく */
    margin: 0;
}

/* お知らせの1行ごとの設定 */
.news-item {
    border-bottom: 1px solid #f2e6d6; /* 優しい薄茶色の区切り線 */
}

.news-item a {
    display: flex;
    align-items: center;  /* 上下の中央揃え */
    padding: 15px 10px;
    text-decoration: none;
    color: #4a3b32;       /* 文字色は少し柔らかい焦げ茶色 */
    transition: background-color 0.3s;
}

/* マウスを載せたときのふんわりした背景色効果 */
.news-item a:hover {
    background-color: #fff9f2; /* 優しいクリーム色 */
}

/* 日付のスタイル */
.news-date {
    font-size: 0.9rem;
    color: #a69385;       /* 日付は少し薄めのブラウン */
    width: 110px;        /* 縦ラインを揃えるために幅を固定 */
    flex-shrink: 0;
}

/* カテゴリ（タグ）の共通スタイル */
.news-category {
    font-size: 0.75rem;
    font-weight: bold;
    padding: 4px 12px;
    border-radius: 4px;
    margin-right: 20px;
    width: 65px;         /* タグの幅を固定 */
    text-align: center;
    flex-shrink: 0;
}

/* カテゴリごとの色分け */
.category-info {
    background-color: #e8ded1; 
    color: #6b5c52;
}

.category-new {
    background-color: #fce2c4; /* メニューバーと同じオレンジベージュ */
    color: #b85c00;            
}

/* タイトルのスタイル */
.news-title {
    margin: 0;
    font-size: 0.95rem;
    white-space: nowrap;      /* 1行に収める */
    overflow: hidden;
    text-overflow: ellipsis;  /* はみ出た部分は「...」にする */
}


/* ==========================================
   アイデア1：ふんわりフェードアウト演出
========================================== */
#splash {
    position: fixed;
    width: 100%;
    height: 100%;
    background-color: #fcf6ed; /* 焼きたて食パンのような優しいベージュ */
    z-index: 9999;             /* 枠線(1005)よりもさらに手前に表示 */
    top: 0;
    left: 0;
    display: flex;
    justify-content: center;   /* 真ん中寄せ */
    align-items: center;       /* 上下も真ん中寄せ */
    opacity: 1;
    visibility: visible;
    transition: opacity 1.5s ease, visibility 1.5s ease; /* 消える時のふわっと感 */
}

/* ロゴ全体の配置 */
#splash-logo {
    text-align: center;
    color: #4a3b32;            /* 茜さんのサイトに合わせた焦げ茶色 */
    font-family: "游ゴシック体", "Yu Gothic", sans-serif;
}

/* 湯気・パンのゆらゆらアニメーション */
.steam-animation {
    font-size: 3.5rem;
    margin-bottom: 10px;
    animation: YuraYura 2s infinite ease-in-out; /* 2秒かけてループ動かす */
}

/* キーフレーム：上下に揺れながら少し拡大縮小する動き */
@keyframes YuraYura {
    0%, 100% { transform: translateY(0) scale(1); }
    50%      { transform: translateY(-10px) scale(1.05); }
}

.splash-title {
    font-size: 2.2rem;
    letter-spacing: 0.1em;
    margin: 10px 0 5px 0;
}

.splash-sub {
    font-size: 0.9rem;
    color: #a69385;
    margin: 0;
}

/* 【合図】読み込み完了後にJavaScriptでこのクラスがつくと透明になって消える */
#splash.is-loaded {
    opacity: 0;
    visibility: hidden;
}

 .menu-grid {
    display: flex;
    justify-content: center;
    gap: 30px;
    flex-wrap: wrap;
    padding: 0 20px;
    max-width: 1000px;    
    margin: 0 auto;
}

.menu-card {
    background-color: var(--white);
    border-radius: 8px;
    overflow: hidden;
    width: 290px;         /* 👈 260pxから「290px」に大きくします！ */
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    transition: transform 0.3s, box-shadow 0.3s;
}


        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .card-img {
            width: 100%;
            aspect-ratio: 1 / 1;
            object-fit: cover;
            background-color: #ddd; 
        }

        .card-body { padding: 12px; }
        .card-title { margin: 0 0 5px 0; font-weight: bold; color: var(--main-color); }
        .card-text { margin: 0; font-size: 0.85rem; color: #666; }





    </style>
</head>
<body>
<div id="border-u"></div>
    <div id="border-r"></div>
    <div id="border-d"></div>
    <div id="border-l"></div>

    <div id="splash">
        <div id="splash-logo">
            <div class="steam-animation">🍞✨</div>
            <h1 class="splash-title">まごころパン屋さん</h1>
            <p class="splash-sub">あたたかい焼きたてをお届けします</p>
        </div>
    </div>
    
<nav class="nav-banner">
    <ul class="nav-list">
        <li>
            <a href="#heading1">
                <span class="en">CONCEPT</span>
                <span class="ja">私たちのこだわり</span>
            </a>
        </li>
        <li>
            <a href="#heading2">
                <span class="en">RECOMMEND</span>
                <span class="ja">おすすめ商品</span>
            </a>
        </li>
        <li>
            <a href="#heading2">
                <span class="en">INTRODUCE</span>
                <span class="ja">商品紹介</span>
            </a>
        </li>
        
   
        
        <li>
            <a href="#heading3">
                <span class="en">SHOP</span>
                <span class="ja">店舗情報（アクセス・営業時間）</span>
            </a>
        </li>
        <li>
            <a href="index.php">
                <span class="en">LOGIN</span>
                <span class="ja">ログイン</span>
            </a>
        </li>
        <li>
            <a href="index.php">
                <span class="en">NewRegistration</span>
                <span class="ja"> 新規登録</span>
            </a>
        </li>
    </ul>
</nav>
<!--
    <h2 id="heading1" >まごころパン屋さん紹介サイト</h2>
    -->
    <div class="text-container">
    <h3>毎朝、しあわせの香りで目が覚める。国産小麦100% ココロとお腹を満たすパン。</h3>
    
    
  </div>
    
    <div class="fade-slider">
        <div class="fade-slide active">
            <img src="images/my_croissant.jpg" alt="食パン">
            <div class="slide-text">毎朝の焼き立てを、あなたに。</div>
        </div>
        
        <div class="fade-slide">
            <img src="images/my_melonpan.jpg" alt="クロワッサン">
            <div class="slide-text">バター香る極上のサクサク感</div>
        </div>
        
        <div class="fade-slide">
            <img src="images/my_creampan.jpg" alt="外観">
            <div class="slide-text">街の小さなまごころパン屋さん</div>
        </div>
    </div>
    
    <div class="text-container">
        <h2>お知らせ</h2>
    </div>

    <div style="max-width: 800px; margin: 0 auto; padding: 0 20px;">
        <ul style="list-style: none; padding: 0; margin: 0;">
        
        
        <li class="news-item">
                <a href="#">
                    <span class="news-date">2026.03.01</span>
                    <span class="news-category category-new">OPEN</span>
                    <p class="news-title">【グランドオープン】焼きたてのしあわせを、お届けしにきました。</p>
                </a>
            </li>
            
         <li class="news-item">
                <a href="#">
                    <span class="news-date">2026.03.05</span>
                    <span class="news-category category-new">新商品</span>
                    <p class="news-title">シナモンロール販売開始しました</p>
                </a>
            </li>
            
            <li class="news-item">
                <a href="#">
                    <span class="news-date">2026.03.10</span>
                    <span class="news-category category-new">新商品</span>
                    <p class="news-title">とろけるクリームパン販売開始しました</p>
                </a>
            </li>
            
            
            <li class="news-item">
                <a href="#">
                    <span class="news-date">2026.07.10</span>
                    <span class="news-category category-info">INFO</span>
                    <p class="news-title">ホームページを開設いたしました</p>
                </a>
            </li>

            <li class="news-item">
                <a href="#">
                    <span class="news-date">2026.07.05</span>
                    <span class="news-category category-new">新商品</span>
                    <p class="news-title">贅沢発酵バターをたっぷり使った「特製クロワッサン」が新登場！</p>
                </a>
            </li>

            <li class="news-item">
                <a href="#">
                    <span class="news-date">2026.06.28</span>
                    <span class="news-category category-info">INFO</span>
                    <p class="news-title">夏期休暇に伴う営業時間変更のお知らせ（8/12〜8/15）</p>
                </a>
            </li>

        </ul>
    </div>

     <div class="text-container">
    <h2 id="heading1" >私たちのこだわり</h2>
    </div>
    <p>まごころを込めて、おいしい笑顔をお届けします。
「まごころパン屋」の朝は、まだ街が眠っている暗い時間から始まります。
香ばしい小麦の香りが工房に広がり、オーブンからパチパチと音が聞こえてくる頃、私たちのパンが焼き上がります。
私たちが大切にしているのは、毎日食べても安心で、カラダが喜ぶパン作り。
厳選した国産小麦を100%使用し、素材本来の甘みと旨みを引き出しました。
「今日のごはんは、まごころパンにしよう」
そう思っていただけるように、今日も一つひとつに想いを込めて、店内で丁寧に焼き上げています。
あなたの一日を豊かにする、小さなしあわせをお持ち帰りください。</p>
    
    
    <div class="text-container">
        <h2 id="heading2">当店イチオシ：特製クロワッサン</h2>
    </div>
    
    <div class="shop-info-container">
        
        <img src="images/my_croissant.jpg" alt="外観" width="300" height="200">
        
        <div class="shop-details">
           <p>
まごころパン屋に一歩足を踏み入れたとき、一番に鼻をくすぐるのが、この「特製クロワッサン」の香ばしい香りです。<br>

自慢の国産小麦に、贅沢な発酵バターをたっぷりと練り込み、職人が何度も丁寧に折り重ねて仕上げました。<br>
一口かじれば、外側は「パリパリッ」と小気味よい音が響き、中は驚くほどしっとり、もっちり。<br>
噛むたびにバターの甘みと小麦の旨みがジュワッと口いっぱいに広がります。<br>

朝食にはもちろん、ほっと一息つきたいおやつタイムにもぴったり。<br>
焼き上がりを狙って、ぜひ一度お召し上がりください。</p>
        </div>
        
    </div>
    <div class="text-container">
        <h2 id="heading2">商品紹介</h2>
    </div>

   
    <div class="menu-grid">
    <div class="menu-card">
    <a href="shop.php?product_id=5">
        <img src="images/my_baguette.jpg" alt="バゲット" class="card-img">
        </a>
        <div class="card-body">
            <p class="card-title">こだわりのバゲット</p>
            <p class="card-text">¥320 / 1本</p>
        </div>
    </div>

    <div class="menu-card">
     <a href="shop.php?product_id=6">
        <img src="images/my_cinnamonroll.jpg" alt="シナモンロール" class="card-img">
         </a>
        <div class="card-body">
            <p class="card-title">シナモンロール</p>
            <p class="card-text">¥300 / 1個</p>
        </div>
    </div>

    <div class="menu-card">
    <a href="shop.php?product_id=2">
        <img src="images/my_creampan.jpg" alt="クリームパン" class="card-img">
        </a>
        <div class="card-body">
            <p class="card-title">とろけるクリームパン</p>
            <p class="card-text">¥220 / 1個</p>
        </div>
    </div>

    <div class="menu-card">
    <a href="shop.php?product_id=1">
        <img src="images/my_croissant.jpg" alt="クロワッサン" class="card-img">
        </a>
        <div class="card-body">
            <p class="card-title">特製クロワッサン</p>
            <p class="card-text">¥250 / 1個</p>
        </div>
    </div>

    <div class="menu-card">
    <a href="shop.php?product_id=3">
        <img src="images/my_melonpan.jpg" alt="メロンパン" class="card-img">
        </a>
        <div class="card-body">
            <p class="card-title">香ばしいメロンパン</p>
            <p class="card-text">¥200 / 1個</p>
        </div>
    </div>

    <div class="menu-card">
    <a href="shop.php?product_id=4">
        <img src="images/my_shokupan.jpg" alt="食パン" class="card-img">
        </a>
        <div class="card-body">
            <p class="card-title">ふんわり食パン</p>
            <p class="card-text">¥380</p>
        </div>
    </div>
</div>

    
    
  <div class="text-container">
        <h2 id="heading3">店舗情報（アクセス・営業時間）</h2>
    </div>
    
    <div class="shop-info-container" style="max-width: 900px;">
        
        <div style="flex: 1; min-width: 300px;">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3243.1157477811066!2d139.34052327528386!3d35.62487293306877!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x60191d0a84b461df%3A0x8c836283d616b451!2z44CSMTkyLTA5MTQg5p2x5Lqs6YO95YWr546L5a2Q5biC54mH5YCJ55S677yR77yU77yQ77yU4oiS77yR!5e0!3m2!1sja!2sjp!4v1783674607080!5m2!1sja!2sjp" 
                    width="100%" 
                    height="350" 
                    style="border:0; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="strict-origin-when-cross-origin">
            </iframe>
        </div>
        
        <div class="shop-details" style="flex: 1; min-width: 250px;">
            <p><strong>まごころパン屋さん</strong><br><br>
            住所：東京都八王子市片倉町1404-1<br>
            営業時間：9:00〜18:30 （売り切れ次第閉店）<br>
            電話番号：111-222-3333<br>
            定休日：年末年始（12/29〜1/3）<br>
            </p>
        </div>
        
    </div>
    
    
  <!--
    <h4>パンの予約はこちらから</h4>
    <form action="index.php" method="POST">
        <button type="submit">ショップを見る</button>
    </form>
    -->

    <script>
        
        const slides = document.querySelectorAll('.fade-slide');
        let currentIndex = 0; 

        function changeSlide() {
            
            slides[currentIndex].classList.remove('active');

            
            currentIndex = (currentIndex + 1) % slides.length;

            
            slides[currentIndex].classList.add('active');
        }

        // 2秒ごとに changeSlide 関数をずっと実行する
        setInterval(changeSlide, 4000);
        
        
        // ページ内のすべての画像やテキストの読み込みが完了したとき
        window.addEventListener('load', function() {
            const splash = document.getElementById('splash');
            
            // ページが開いてから「0.8秒」だけロゴをしっかり見せてから、消す合図を送る
            setTimeout(function() {
                splash.classList.add('is-loaded');
            }, 800); 
        });
    </script>
</body>
</html>
