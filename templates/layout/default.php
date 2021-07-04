<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->fetch('title') ?></title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->fetch('meta') ?>

    <?= $this->Html->css(['cake.css', 'uikit.min.css', 'artistreg.css']) ?>
    <?= $this->Html->script(['uikit.min.js', 'uikit-icons.min.js', 'artistreg.js']) ?>

    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <header>
        <h1>Eurofurence Online 2021 &ndash; Artist Registration</h1>
    </header>
    <main>
        <?= $this->Flash->render() ?>
        <?= $this->fetch('content') ?>
    </main>
    <footer>
        <div>
            <div>
                <h3>Support:</h3>
                <ul class="uk-list">
					<li><a href="https://t.me/jyanon" target="_blank"><span uk-icon="microphone"></span>Organizer</a></li>
					<li><a href="https://t.me/draconigen" target="_blank"><span uk-icon="lifesaver" class="ef-big-icon-fix"></span>Tech Support</a></li>
				</ul>
            </div>
            <div>
                <h3>Find us on:</h3>
                <div class="uk-button-group">
                    <a target="_blank" href="https://www.eurofurence.org/" class="uk-icon-button" uk-icon="icon:home"></a> 
                    <a target="_blank" href="https://www.twitter.com/eurofurence" class="uk-icon-button" uk-icon="icon:twitter"></a> 
                    <a target="_blank" href="https://www.facebook.com/eurofurence" class="uk-icon-button" uk-icon="icon:facebook"></a> 
                    <a target="_blank" href="https://discord.com/invite/VMESBMM" class="uk-icon-button" uk-icon="icon:discord"></a>
                    <a target="_blank" href="https://vimeo.com/eurofurence" class="uk-icon-button" uk-icon="icon:vimeo"></a>
                </div>
            </div>
            <div>
                <h3>Legal:</h3>
                <ul class="uk-list">
                    <li><a href="https://help.eurofurence.org/legal/imprint"><span uk-icon="icon:bookmark"></span>Imprint &amp; Legal Notice</a></li>
                    <li><a href="https://help.eurofurence.org/legal/attributions"><span uk-icon="icon:heart" class="ef-big-icon-fix"></span>Site Attributions</a></li>
                </ul>
            </div>
        </div>
    </footer>
</body>
</html>
