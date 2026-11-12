<?php
/**
 * CIO hero + article (React: CIOAddress).
 *
 * @package TGIT_Quarterly
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hero_img = tgit_quarterly_img_uri( 'hero-pattern.jpg' );
$portrait = tgit_quarterly_img_uri( 'cio-portrait.jpg' );
?>
<section id="cio-address" class="relative">
	<div class="relative h-[60vh] overflow-hidden md:h-[70vh]">
		<img src="<?php echo esc_url( $hero_img ); ?>" alt="" class="absolute inset-0 h-full w-full object-cover" width="1920" height="1080" loading="eager" decoding="async" />
		<div class="absolute inset-0 bg-primary/80"></div>
		<div class="relative z-10 flex h-full items-end section-padding pb-12 md:pb-20">
			<div class="scroll-reveal">
				<p class="mb-4 font-body text-xs font-semibold uppercase tracking-[0.3em] text-accent">
					<?php esc_html_e( 'February 2026 · Monthly Address', 'tgit-quarterly' ); ?>
				</p>
				<h1 class="max-w-3xl font-editorial text-3xl leading-[1.1] text-primary-foreground text-balance md:text-5xl lg:text-6xl">
					<?php esc_html_e( 'Navigating the Next Frontier of Enterprise Intelligence', 'tgit-quarterly' ); ?>
				</h1>
				<div class="mt-6 h-[2px] w-16 bg-accent"></div>
			</div>
		</div>
	</div>

	<div class="section-padding py-16 md:py-24">
		<div class="mx-auto grid max-w-6xl grid-cols-1 gap-12 lg:grid-cols-12 lg:gap-16">
			<div class="scroll-reveal lg:col-span-4" style="--reveal-delay: 0s">
				<div class="lg:sticky lg:top-28">
					<div class="relative mb-6">
						<img
							src="<?php echo esc_url( $portrait ); ?>"
							alt="<?php esc_attr_e( 'CIO Portrait', 'tgit-quarterly' ); ?>"
							class="aspect-[3/4] w-full max-w-[280px] object-cover grayscale transition-all duration-700 hover:grayscale-0"
							width="280"
							height="374"
							loading="lazy"
							decoding="async"
						/>
						<div class="absolute bottom-0 left-0 right-0 h-1 max-w-[280px] gold-gradient"></div>
					</div>
					<h3 class="font-editorial text-xl font-semibold text-foreground"><?php esc_html_e( 'Richard Harmon', 'tgit-quarterly' ); ?></h3>
					<p class="mt-1 font-body text-sm text-muted-foreground"><?php esc_html_e( 'Chief Information Officer', 'tgit-quarterly' ); ?></p>
					<div class="editorial-divider-left mb-4 mt-4"></div>
					<p class="font-body text-xs leading-relaxed text-muted-foreground">
						<?php esc_html_e( "25 years in enterprise technology leadership. Former VP of Digital Transformation at Global Media Corp. Driving our organisation's technology-first future.", 'tgit-quarterly' ); ?>
					</p>
				</div>
			</div>

			<div class="lg:col-span-8">
				<div class="scroll-reveal" style="--reveal-delay: 0s">
					<p class="mb-8 font-body text-lg font-light leading-relaxed text-foreground first-letter:float-left first-letter:mr-3 first-letter:mt-1 first-letter:font-editorial first-letter:text-5xl first-letter:font-bold md:text-xl">
						<?php esc_html_e( 'As we close the first quarter, I want to reflect on the remarkable strides our teams have made in redefining what technology means for our organisation. This month marks a pivotal moment — not just in delivery, but in how we think about the intersection of data, automation, and human creativity.', 'tgit-quarterly' ); ?>
					</p>
				</div>

				<div class="scroll-reveal my-10 border-l-2 border-accent bg-editorial-quote px-8 py-8" style="--reveal-delay: 0.1s">
					<blockquote>
						<p class="font-editorial text-xl italic leading-relaxed text-foreground md:text-2xl">
							<?php esc_html_e( '"We are not simply adopting AI — we are building the organisational muscle to think alongside it."', 'tgit-quarterly' ); ?>
						</p>
						<cite class="mt-4 block font-body text-xs uppercase tracking-widest text-muted-foreground not-italic">
							<?php esc_html_e( '— Richard Harmon, CIO', 'tgit-quarterly' ); ?>
						</cite>
					</blockquote>
				</div>

				<div class="scroll-reveal" style="--reveal-delay: 0.15s">
					<h2 class="mb-6 mt-12 font-editorial text-2xl font-semibold text-foreground md:text-3xl">
						<?php esc_html_e( 'Strategic Priorities in Focus', 'tgit-quarterly' ); ?>
					</h2>
					<p class="mb-6 font-body text-base leading-relaxed text-muted-foreground">
						<?php esc_html_e( 'Our cloud migration programme has reached 78% completion — ahead of schedule by a full quarter. The infrastructure team, led by Priya Sharma, has delivered exceptional results while maintaining zero unplanned downtime. This achievement sets the foundation for everything we plan to build in the coming months.', 'tgit-quarterly' ); ?>
					</p>
					<p class="mb-6 font-body text-base leading-relaxed text-muted-foreground">
						<?php esc_html_e( 'On the AI front, our editorial automation pilot has shown a 34% improvement in content processing speed without compromising editorial quality. These are not just numbers — they represent hours returned to our journalists and editors to do what they do best: tell stories that matter.', 'tgit-quarterly' ); ?>
					</p>
				</div>

				<div class="scroll-reveal my-12 grid grid-cols-3 gap-4 border-y border-editorial-border py-8" style="--reveal-delay: 0.2s">
					<div class="text-center">
						<p class="font-editorial text-3xl font-bold text-foreground md:text-4xl">78%</p>
						<p class="mt-2 font-body text-[10px] uppercase tracking-wider text-muted-foreground md:text-xs"><?php esc_html_e( 'Cloud Migration Complete', 'tgit-quarterly' ); ?></p>
					</div>
					<div class="text-center">
						<p class="font-editorial text-3xl font-bold text-foreground md:text-4xl">34%</p>
						<p class="mt-2 font-body text-[10px] uppercase tracking-wider text-muted-foreground md:text-xs"><?php esc_html_e( 'Processing Speed Gain', 'tgit-quarterly' ); ?></p>
					</div>
					<div class="text-center">
						<p class="font-editorial text-3xl font-bold text-foreground md:text-4xl">0</p>
						<p class="mt-2 font-body text-[10px] uppercase tracking-wider text-muted-foreground md:text-xs"><?php esc_html_e( 'Unplanned Downtime Hours', 'tgit-quarterly' ); ?></p>
					</div>
				</div>

				<div class="scroll-reveal" style="--reveal-delay: 0.25s">
					<h2 class="mb-6 font-editorial text-2xl font-semibold text-foreground md:text-3xl">
						<?php esc_html_e( 'Looking Ahead', 'tgit-quarterly' ); ?>
					</h2>
					<p class="mb-6 font-body text-base leading-relaxed text-muted-foreground">
						<?php esc_html_e( "March brings the go-live of our unified data platform — a project eighteen months in the making. I encourage every department head to engage with the training sessions our team has prepared. The tools we're building are only as powerful as the people who wield them.", 'tgit-quarterly' ); ?>
					</p>
					<p class="font-body text-base leading-relaxed text-muted-foreground">
						<?php esc_html_e( "To every member of the IT department: your work is not invisible. This publication exists because what you build deserves to be seen, understood, and celebrated. Let's keep pushing.", 'tgit-quarterly' ); ?>
					</p>
				</div>
			</div>
		</div>
	</div>
</section>
