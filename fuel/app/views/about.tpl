{{extends file='layout.tpl'}}

{{block name=content}}
		<main id="about">
			<div class="main-contents container">
				<div class="inner-container">
					<section class="mb-4">
						<h2>Cities History Project について</h2>
						<p>Cities History Project（行政区域変遷データベース）は、日本全国の自治体合併を網羅し、「時系列順の自治体の合併履歴」「特定の日付に存在した自治体」などを分かりやすく表示することが目的のデータベースです。</p>
						<p>現時点では、1878年(明治11年)の「郡区町村編制法」以降の合併を網羅することを目標としています。ウェブでもあまり情報が載っていない明治22年の「市制・町村制」施行、いわゆる「明治の大合併」前も網羅する予定です。（明治11年以降としている理由は、それ以前だと廃藩置県や大区小区制など自治体の変化が非常に激しいのと、明治5年末に旧暦から新暦に変わったことで日付情報をうまく扱えないためです）</p>
					</section>
					<section class="mb-4">
						<h2>連絡先</h2>
						<p>
							Twitter : <a href="https://twitter.com/RiinaKw" target="_blank">@RiinaKw</a><br />
							メール : {{mailto address='riinak.tv@gmail.com' encode='javascript'}}
						</p>
						<p>掲載データに誤りがある場合やバグ報告、データ追加依頼など、ご遠慮無くご連絡ください。</p>
					</section>
				</div>
			</div>
		</main>

{{/block}}
