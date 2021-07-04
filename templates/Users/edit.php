<?php
$links = [
	"mail" => "eMail",
    "homepage" => "Homepage",
    "furaffinity" => "FurAffinity",
    "patreon" => "Patreon",
    "kofi" => "Ko-Fi",
    "furbuy" => "Furbuy",
    "etsy" => "Etsy",
    "deviantart" => "DeviantArt",
    "inkbunny" => "InkBunny",
    "weasyl" => "Weasyl",
    "furrynetwork" => "FurryNetwork",
    "sofurry" => "SoFurry",
    "telegram" => "Telegram",
    "discord" => "Discord#0000",
    "twitter" => "Twitter",
    "facebook" => "Facebook",
    "youtube" => "Youtube",
    "twitch" => "Twitch",
    "picarto" => "Picarto",
];
?>

<section>
	<h1>EFO 2021 Artist Signup</h1>
	
	<div class="uk-text-right">
		Logged in as <strong><?= $subject->email ?></strong> | <a href="logout"><span uk-icon="sign-out"></span>Log out</a>
	</div>

	<div uk-alert class="uk-alert-primary">
		<p>Everything entered into the form below will be used for the Eurofurence Online 2021 website and <strong>only</strong> for EFO2021.<br />
			No data nor registration will be carried over to the next year.</p>
	</div>
	
	<?= $this->Form->create($subject, ['id' => 'artist-edit']) ?>
	
	<fieldset>
		<legend>Public Information</legend>
		
		<h3>Dealer Name</h3>
			
		<?php if (in_array("name_too_short", $errors)) { ?>
			<div uk-alert class="uk-alert-danger">
				<strong>Error: Name is too short.</strong>
			</div>
		<?php } ?>
		
		<?php if (in_array("name_too_long", $errors)) { ?>
			<div uk-alert class="uk-alert-danger">
				<strong>Error: Name is too long.</strong>
			</div>
		<?php } ?>
		
		<label>
			<input type="text" id="name" class="uk-input" placeholder="How will you appear in the artist listing? (3 - 100 characters)" maxlength="100" />
		</label>
		
		<hr />
		
		<h3>About You</h3>
		
		<?php if (in_array("about_too_long", $errors)) { ?>
			<div uk-alert class="uk-alert-danger">
				<strong>Error: 'About You' is too long.</strong>
			</div>
		<?php } ?>
		
		<label>
			<textarea id="about" class="uk-textarea" placeholder="Talk about yourself and what you do or sell. (0 - 4000 characters)" maxlength="4000"></textarea>
		</label>
		
		<hr />
		
		<h3>Tags</h3>
		
		<div uk-alert class="uk-alert-primary">
			<p>Enter roughly three to five singular tags that best describe your merchandise. Please enter them separated by <strong>spaces</strong>. Commonly used tags are for example: <span id="suggested-tags">art commission digital traditional feral</span></p>
		</div>
		
		<?php if (in_array("tags_too_long", $errors)) { ?>
			<div uk-alert class="uk-alert-danger">
				<strong>Error: Tags are too long.</strong>
			</div>
		<?php } ?>
		
		<input type="text" class="uk-input" id="tags" placeholder="Your space-separated tags (0 - 300 characters)" maxlength="300" />

		<hr />
		
		<h3>Links</h3>
		
		<div uk-alert class="uk-alert-primary">
			<p>Enter below your <strong>name / handle</strong> for each service; the rest of the URL will be completed automatically. You can test if the link works in the preview below.</p>
		</div>
		
		<div id="artist-links" uk-grid>
			<?php
				foreach ($links as $key => $name) {
					if (in_array("links_${key}_too_short", $errors)) {
						echo "<div uk-alert class=\"uk-alert-danger uk-width-1-1\">
							<strong>Error: '${name}' link too short.</strong>
						</div>";
					}
					if (in_array("links_${key}_too_long", $errors)) {
						echo "<div uk-alert class=\"uk-alert-danger uk-width-1-1\">
							<strong>Error: '${name}' link too long.</strong>
						</div>";
					}
					echo "<label class=\"icon-${key}\">
						<input type=\"text\" id=\"${key}\" class=\"uk-input\" placeholder=\"${name}\" maxlength=\"100\" />
					</label>\n";
				}
			?>
		</div>
		
		<hr />
		
		<button type="button" id="submitButton" class="uk-button uk-button-primary">Save</button>
	
	
	<?= $this->Form->control('data', ['id' => 'artist-data', 'hidden' => true, 'label' => false]) ?>
	   
	<?= $this->Form->end() ?>
	</fieldset>
	
	<fieldset>
		<legend>Gallery</legend>
		
		<h3>Banner</h3>
		
		<div uk-alert class="uk-alert-primary">
			<p>This banner will be displayed along with your artist listing entry and must be 400x200px.</p>
		</div>
		
		<div class="js-upload-banner uk-placeholder uk-text-center">
			<span uk-icon="icon: cloud-upload"></span>
			<span class="uk-text-middle">Upload a file by dropping it here or</span>
			<div uk-form-custom>
				<input type="file">
				<span class="uk-link">selecting one</span>.
			</div>
		</div>

		<progress id="js-progressbar-banner" class="uk-progress" value="0" max="100" hidden></progress>

		<hr />
		
		<h3>Showcase</h3>
		
		<div uk-alert class="uk-alert-primary">
			<p>Upload up to six images for a brief showcase of your work. Each image should be smaller than 1000x1000px.</p>
		</div>
		
		<div class="js-upload-showcase uk-placeholder uk-text-center">
			<span uk-icon="icon: cloud-upload"></span>
			<span class="uk-text-middle">Upload files by dropping them here or</span>
			<div uk-form-custom>
				<input type="file" multiple>
				<span class="uk-link">selecting them</span>.
			</div>
		</div>

		<progress id="js-progressbar-showcase" class="uk-progress" value="0" max="100" hidden></progress>
	</fieldset>
	
	<fieldset>
		<legend>Preview</legend>
	
	</fieldset>
</section>

<script>

var data = {links: {}};

function update_data() {
	data.name = document.getElementById("name").value;
	data.about = document.getElementById("about").value;
	data.tags = document.getElementById("tags").value;
	
	let links = document.querySelectorAll('#artist-links input');
	for (let i = 0; i < links.length; i++) {
		if (links[i].value !== "")
			data.links[links[i].id] = links[i].value;
		else 
			delete data.links[links[i].id];
	}
}

function update_preview() {
	
}

for (let input of document.querySelectorAll("input, textarea")) {
	input.addEventListener('change', (event) => {
		update_data();
		update_preview();
	});
}

(document.getElementById("submitButton")).addEventListener('click', (event) => {
	update_data();
	document.getElementById('artist-data').value = JSON.stringify(data);
	(document.getElementById('artist-edit')).submit();
});

document.addEventListener('DOMContentLoaded', () => {
	if (document.getElementById('artist-data').value == "")
		return;
	
	data = JSON.parse(document.getElementById('artist-data').value);
	
	document.getElementById("name").value = data.name;
	document.getElementById("about").value = data.about;
	document.getElementById("tags").value = data.tags;
	
	for (key in data.links) {
		document.getElementById(key).value = data.links[key];
	}
});
</script>


<script>
    var bar_banner = document.getElementById('js-progressbar-banner');

    UIkit.upload('.js-upload-banner', {
        url: '<?= $banner_upload_url ?>',
        multiple: false,

        beforeSend: function () {
            console.log('beforeSend', arguments);
        },
        beforeAll: function () {
            console.log('beforeAll', arguments);
        },
        load: function () {
            console.log('load', arguments);
        },
        error: function () {
            console.log('error', arguments);
        },
        complete: function () {
            console.log('complete', arguments);
        },

        loadStart: function (e) {
            console.log('loadStart', arguments);

            bar_banner.removeAttribute('hidden');
            bar_banner.max = e.total;
            bar_banner.value = e.loaded;
        },

        progress: function (e) {
            console.log('progress', arguments);

            bar_banner.max = e.total;
            bar_banner.value = e.loaded;
        },

        loadEnd: function (e) {
            console.log('loadEnd', arguments);

            bar_banner.max = e.total;
            bar_banner.value = e.loaded;
        },

        completeAll: function () {
            console.log('completeAll', arguments);

            setTimeout(function () {
                bar_banner.setAttribute('hidden', 'hidden');
            }, 1000);

            alert('Upload Completed');
        }
    });
</script>

