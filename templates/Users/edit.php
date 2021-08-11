<?php
$links = [
	"mail" => "eMail",
    "homepage" => "Homepage",
    "furaffinity" => "FurAffinity",
    "patreon" => "Patreon",
    "kofi" => "Ko-Fi",
    // "furbuy" => "Furbuy",
    "etsy" => "Etsy",
    "deviantart" => "DeviantArt",
    "inkbunny" => "InkBunny",
    "weasyl" => "Weasyl",
    "furrynetwork" => "FurryNetwork",
    "sofurry" => "SoFurry",
    "telegram" => "Telegram",
    "discord" => "Discord",
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
		<p>Everything entered into the form below will be used for the Eurofurence Online 2021 website and EFO2021 services only.
No data nor registration will be carried over to the next year or other events.</p>
		<p>Anyone who offers furry related wares, which can be legally acquired in the European Union, are eligible to register as Artist or Dealer.
We retain the right to delete specific or all elements of a registration, especially in case of malicious intent, propagation of religious or political messages, questionable quality, and uncensored adult material.
Advertising adult wares is allowed as long as the advertisement is censored.</p>

		<p>For questions in regard of the registration form, please join <a href="https://discord.com/invite/VMESBMM" target="_blank">our official Discord Server</a>.</p>
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
			<input type="text" id="name" class="uk-input" placeholder="With what name do you want to appear in the dealer listing on the website? (3 - 100 characters)" maxlength="100" />
		</label>
		
		<hr />
		
		<h3>About You</h3>
		
		<?php if (in_array("about_too_long", $errors)) { ?>
			<div uk-alert class="uk-alert-danger">
				<strong>Error: 'About You' is too long.</strong>
			</div>
		<?php } ?>
		
		<label>
			<textarea id="about" class="uk-textarea" placeholder="Talk about yourself and what you do or sell. (0 – 1500 characters)" maxlength="1500"></textarea>
		</label>
		
		<hr />
		
		<h3>Tags</h3>
		
		<div uk-alert class="uk-alert-primary">
			<p>Enter three to five singular tags that best describe your services and merchandise.
			<br />Please enter them <strong>separated by spaces</strong>. Commonly used tags are for example:<br />Artwork Fursuits Toys Clothing Adult</p>
		</div>
		
		<?php if (in_array("tags_too_long", $errors)) { ?>
			<div uk-alert class="uk-alert-danger">
				<strong>Error: Tags are too long.</strong>
			</div>
		<?php } ?>
		
		<?php if (in_array("tags_invalid_separator", $errors)) { ?>
			<div uk-alert class="uk-alert-danger">
				<strong>You seem to be using anything else than spaces to separate your tags. Make sure you use spaces!</strong>
			</div>
		<?php } ?>
		
		<input type="text" class="uk-input" id="tags" placeholder="Your space-seperated tags (0 - 200 characters)" maxlength="200" />

		<hr />
		
		<h3>Links</h3>
		
		<div uk-alert class="uk-alert-primary">
			<p>Enter below your <strong>name / handle</strong> for each media service you would like to be contacted with; the rest of the URL will be completed automatically. 
You can test if the link works in the preview below.<br />
You must enter your Twitter handle until the 5th of September if you would like to be announced in our “Dealer Countdown” on our official twitter. <br />
You must enter your Discord name until the 22th of September if you would like to receive the Registered Dealer 2021 role and dealer rights on the Server.</p>
		</div>
		
		<div id="artist-links" uk-grid>
			<?php
				foreach ($links as $key => $name) {
					if (in_array("links_${key}_too_short", $errors)) {
						echo "<div uk-alert class=\"uk-alert-danger uk-width-1-1\">
							<strong>Error: '${name}' handle too short.</strong>
						</div>";
					}
					if (in_array("links_${key}_too_long", $errors)) {
						echo "<div uk-alert class=\"uk-alert-danger uk-width-1-1\">
							<strong>Error: '${name}' handle too long.</strong>
						</div>";
					}
					if (in_array("links_${key}_is_url", $errors)) {
						echo "<div uk-alert class=\"uk-alert-danger uk-width-1-1\">
							<strong>Error: '${name}' input is a URL. Please provide only your USER NAME.</strong>
						</div>";
					}
					if ($key === 'discord' && in_array("links_discord_missing_suffix", $errors)) {
						echo "<div uk-alert class=\"uk-alert-danger uk-width-1-1\">
							<strong>Error: Your discord tag is incomplete without your discriminator (the four digits following a # after your username). It should be something like: draconigen#5175</strong>
						</div>";
					}
					echo "<label class=\"icon-${key}\">
						<input type=\"text\" id=\"${key}\" class=\"uk-input\" placeholder=\"${name}\" maxlength=\"100\" />
					</label>\n";
				}
			?>
		</div>
		
		<h4>Main Contact</h4>
		
		<div uk-alert class="uk-alert-primary">
			<p>Select which of the provided media links above to prefer when contacting you. The selected account will be highlighted above all pther provided links.</p>
		</div>

		<select id="main-contact" class="uk-select"></select>
		
		<hr />
		<button type="button" id="submitButton" class="uk-button uk-button-primary">Save</button>
	
	
	<?= $this->Form->control('data', ['id' => 'artist-data', 'hidden' => true, 'label' => false]) ?>
	   
	<?= $this->Form->end() ?>
	</fieldset>
	
	<fieldset>
		<legend>Image Upload</legend>
		
		<h3>Avatar</h3>
		
		<div uk-alert class="uk-alert-primary">
			<p>An Avatar to present your yourself and/or brand.<br />This avatar will be displayed along with your artist listing entry and should have a square aspect ratio of 1:1 with at least 100x100px. The file must be not larger than 5MB and be either .jpg or .png.<br />In case no Avatar is being uploaded you will be represented by a default picture of the EFO 2021 Logo. </p>
		</div>
		
		<div class="js-upload-efo js-upload-avatar uk-placeholder uk-text-center">
			<div id="display-avatar" class="efo-upload"></div>
			<span uk-icon="icon: cloud-upload"></span>
			<span class="uk-text-middle">Upload a file by dropping it here or</span>
			<div uk-form-custom>
				<input type="file">
				<span class="uk-link">selecting one</span>.
			</div>
		</div>

		<progress id="js-progressbar-avatar" class="uk-progress" value="0" max="100" hidden></progress>

		<hr />
		
		<h3>Advertisement (optional)</h3>
		
		<div uk-alert class="uk-alert-primary">
			<p>An advertisement to represent your business and deals on our website. <br />It will displayed above your avatar and be the first picture every attendee will see about your brand. The Aspect ratio must be 1:1 in landscape (e.g. 500x500px @ 72dpi). The file has to be smaller than 5MB and be either .jpg or .png.<br />In case no Advertisement was uploaded only the avatar will be shown on the website.</p>
		</div>
		
		<div class="js-upload-efo js-upload-advertisement uk-placeholder uk-text-center">
			<div id="display-advertisement" class="efo-upload"></div>
			<span uk-icon="icon: cloud-upload"></span>
			<span class="uk-text-middle">Upload a file by dropping it here or</span>
			<div uk-form-custom>
				<input type="file">
				<span class="uk-link">selecting one</span>.
			</div>
		</div>

		<progress id="js-progressbar-advertisement" class="uk-progress" value="0" max="100" hidden></progress>
		
		<hr />
		
		<h3>Showcase</h3>
		
		<div uk-alert class="uk-alert-primary">
			<p>Upload up to six images for a brief showcase of your services or work. Each image should be smaller than 5MB and either be .jpg or .png.</p>
		</div>
		
		<div class="js-upload-efo js-upload-showcase uk-placeholder uk-text-center">
			<div id="display-showcase" class="efo-upload"></div>
			<span uk-icon="icon: cloud-upload"></span>
			<span class="uk-text-middle">Upload files by dropping them here or</span>
			<div uk-form-custom>
				<input type="file" multiple>
				<span class="uk-link">selecting them</span>.
			</div>
		</div>

		<progress id="js-progressbar-showcase" class="uk-progress" value="0" max="100" hidden></progress>

		<hr />

		<h3>Virtual Conspace Texture (optional)</h3>
		
		<div uk-alert class="uk-alert-primary">
			<p>A Texture that will be used to create your personal booth in the Virtual Estrel hotel in VR Chat. <br >Please download <a href="../download/VCTexture_Template.png" target="_blank">the official VCTexture_Template.png</a> and edit it with your intended advertisements; <a href="../download/booth-example.jpg" target="_blank">here is an example of the product</a>. <br />Please fill out the full template inlcuding the black highlighted area. Be aware that the black highlighted area in the Banner might be covered by the 3d model.<br />Only fully filled out templates will be accepted to be displayed in the VR Chat world.<br />Depending on the interest for this service, we might be forced to limit the number of booths we will be able to display in the game world. File must be .jpg or .png and smaller than 25MB.</p>
		</div>
		
		<div class="js-upload-efo js-upload-banner uk-placeholder uk-text-center">
			<div id="display-banner" class="efo-upload"></div>
			<span uk-icon="icon: cloud-upload"></span>
			<span class="uk-text-middle">Upload a file by dropping it here or</span>
			<div uk-form-custom>
				<input type="file">
				<span class="uk-link">selecting one</span>.
			</div>
		</div>

		<progress id="js-progressbar-banner" class="uk-progress" value="0" max="100" hidden></progress>
	</fieldset>
	
	<fieldset>
		<legend>Preview</legend>
		<p class="uk-text-center">&lt; not available yet &gt;</p>
	</fieldset>
	
	<div id="error-report" uk-modal>
		<div class="uk-modal-dialog uk-modal-body">
			<h2 class="uk-modal-title">Error Report</h2>
			<pre><ul id="error-report-content"></ul></pre>
			<p class="uk-text-right">
				<button class="uk-button uk-button-default uk-modal-close" type="button">OK</button>
			</p>
		</div>
	</div>
</section>

<?php 
?>
<script>
	var data = {"links":{},"name":"","about":"","tags":"","showcase":[],"avatar":"", "banner":"", "advertisement":"", "main":""};
	try {
		// data = JSON.parse("<?= $subject->data ?>");
		data = <?= $subject->data ?>;
		console.info("data parsed: ", data);
	}
	catch(ex) {
		document.getElementById('error-report-content').innerText = `Error loading data, please report the following error details to tech support:\n${ex}`;
		UIkit.modal(document.getElementById('error-report')).show();
	}
	
	document.getElementById("name").value = data.name;
	document.getElementById("about").value = data.about;
	document.getElementById("tags").value = data.tags;
	
	for (key in data.links) {
		document.getElementById(key).value = data.links[key];
	}

	function update_data() {
		data.name = document.getElementById("name").value;
		data.about = document.getElementById("about").value;
		data.tags = document.getElementById("tags").value;
		data.main = document.getElementById("main-contact").value;
		
		let links = document.querySelectorAll('#artist-links input');
		for (let i = 0; i < links.length; i++) {
			if (links[i].value !== "")
				data.links[links[i].id] = links[i].value;
			else 
				delete data.links[links[i].id];
		}
	}
	
	function update_files_overview() {
		let avatar = document.getElementById('display-avatar');
		let advertisement = document.getElementById('display-advertisement');
		let banner = document.getElementById('display-banner');
		let showcase = document.getElementById('display-showcase');
		
		avatar.innerHTML = "";
		if (data.avatar != "")
			avatar.appendChild(thumb(data.avatar));
					
		advertisement.innerHTML = "";
		if (data.advertisement != "")
			advertisement.appendChild(thumb(data.advertisement));
								
		banner.innerHTML = "";
		if (data.banner != "")
			banner.appendChild(thumb(data.banner));
					
		showcase.innerHTML = "";
		for (let i = 0; i < data.showcase.length; i++) {
			showcase.appendChild(thumb(data.showcase[i]));
		}
	}

	function update_main_select() {
		artistLinksMainSelect.innerHTML = "";
		
		let none = document.createElement('option');
		none.value = "";
		none.innerText = '(none - all are equal)';		
		if (data.hasOwnProperty('main')) {
			if (data.main === "")
				none.selected = true;
		}
		artistLinksMainSelect.appendChild(none);

		artistLinksInputs.forEach(inp => {
			if (inp.value !== "") {
				let opt = document.createElement('option');
				opt.value = inp.id;
				opt.innerText = inp.placeholder;
				if (inp.id === data.main)
					opt.selected = true;
				artistLinksMainSelect.appendChild(opt);
			}
		});
	}
	
	function thumb(path) {
		let article = document.createElement('article');
		let img = document.createElement('img');
		let button = document.createElement('button');
		
		img.alt = path.replace(/^.*[\\\/]/, '');
		img.src = '../' + path;
		
		button.type = 'button';
		button.setAttribute('uk-icon', 'trash');
		button.addEventListener('click', (e) => {remove_image(path)});
		
		article.appendChild(img);
		article.appendChild(button);
		
		return article;
	}
	
	async function remove_image(path) {
		let response = await fetch('<?= $remove_image_url ?>', {
			method: 'DELETE',
			headers: {'Content-Type': 'application/json'},
			body: JSON.stringify(path)
		});
		
		let r = await response.json();
		
		console.log('delete response, status ' + response.status, r);
				
		data.avatar = r.avatar;
		data.advertisement = r.advertisement;
		data.banner = r.banner;
		data.showcase = r.showcase;
			
		update_files_overview();
		
		if (r.hasOwnProperty('errors')) {
			if (r.errors.length != 0) {
				for(let i = 0; i < r.errors.length; i++) {
					document.getElementById('error-report-content').insertAdjacentHTML('beforeend', `<li>${r.errors[i]}</li>`);
				}
				UIkit.modal(document.getElementById('error-report')).show();
			}
		}		
	}
	
	function process_upload_response(e) {
		let r = JSON.parse(e.target.response);
		
		console.info('upload response ' + e.target.status, r);
		
		data.avatar = r.avatar;
		data.advertisement = r.advertisement;
		data.banner = r.banner;
		data.showcase = r.showcase;
		
		update_files_overview();		
		
		if (r.hasOwnProperty('errors')) {
			if (r.errors.length != 0) {
				for(let i = 0; i < r.errors.length; i++) {
					document.getElementById('error-report-content').insertAdjacentHTML('beforeend', `<li>${r.errors[i]}</li>`);
				}
				UIkit.modal(document.getElementById('error-report')).show();
			}
		}
	}
	
	function update_preview() {
		
	}
	
	update_files_overview();

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

	document.getElementById('error-report').addEventListener('hidden', (e) => {
		document.getElementById('error-report-content').innerHTML = "";
	});
	
	setInterval(async () => {
		let response = await fetch('<?= $keepalive_url ?>');

		if (!response.ok) {
			document.getElementById('error-report-content').innerText = "Connection lost! You might have been logged out. To prevent loss of unsaved input, close this message, copy any unsaved input and press F5 to reload the website and log back in.";
			UIkit.modal(document.getElementById('error-report')).show();
		}
		
	}, 60000);
</script>

<script>
	for (const [key, value] of Object.entries({
		"avatar": {"multiple": false, "url": '<?= $avatar_upload_url ?>'},
		"advertisement": {"multiple": false, "url": '<?= $advertisement_upload_url ?>'},
		"banner": {"multiple": false, "url": '<?= $banner_upload_url ?>'},
		"showcase": {"multiple": true, "url": '<?= $showcase_upload_url ?>'},
	})) {
		const bar = document.getElementById(`js-progressbar-${key}`);

		UIkit.upload(`.js-upload-${key}`, {
			url: value.url,
			multiple: value.multiple,

			loadStart: function (e) {
				bar.removeAttribute('hidden');
				bar.max = e.total;
				bar.value = e.loaded;
			},

			progress: function (e) {
				bar.max = e.total;
				bar.value = e.loaded;
			},
			
			loadEnd: function (e) {
				process_upload_response(e);			
				bar.max = e.total;
				bar.value = e.loaded;
				setTimeout(function () {
					bar.setAttribute('hidden', 'hidden');
				}, 1000);
			}
		});
	}
</script>

<script>
	var artistLinksInputs = document.querySelectorAll("#artist-links input");
	var artistLinksMainSelect = document.getElementById("main-contact");

	for (let input of artistLinksInputs) {
		input.addEventListener('change', (event) => {
			update_main_select();
		});
	}

	update_main_select();

	artistLinksMainSelect.addEventListener('change', (event) => {
		data.main = artistLinksMainSelect.value;
	});
</script>


