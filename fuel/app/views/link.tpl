{{extends file='layout.tpl'}}

{{block name=content}}
		<main id="about">
			<div class="main-contents container">
				<div class="inner-container">
					<section class="mb-4">
						<h2>外部リンク</h2>
						<p>自治体の合併についての情報を掲載している他サイト様のご紹介です。</p>

						<dl>
							<dt>
								<a href="http://geoshape.ex.nii.ac.jp/city/" target="blannk">歴史的行政区域データセットβ版 | Geoshapeリポジトリ</a>
							</dt>
							<dd>
								全国の自治体区域（過去に存在したものを含む）の地図データを公開しているサイト様です。<br />
								Cities History Project でも地図データを利用させていただいています。
							</dd>
							{{*
							<dt>
								<a href="http://mujina.sakura.ne.jp/history/index.html" target="blannk">市町村変遷パラパラ地図</a>
							</dt>
							<dd>
								自治体合併をパラパラまんがのように時系列の画像で表現した、面白い切り口のサイト様です。
							</dd>
							*}}
						</dl>
					</section>
				</div>
			</div>
		</main>

{{/block}}
