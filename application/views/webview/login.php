<style>
	.login_form {
		background-color: var(--surface-color);
		height: 100%;
		padding: 30px;
		box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
	}
</style>

<main class="main">
	<!-- Contact Section -->
	<section id="contact" class="contact section">

		<!-- Section Title -->
		<div class="container section-title" data-aos="fade-up">
			<!-- <span>Section Title</span> -->
			<h2>Login</h2>
			<!-- <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p> -->
		</div><!-- End Section Title -->

		<div class="container" data-aos="fade-up" data-aos-delay="100">

			<div class="row gy-2">

				<div class="col-lg-6 offset-lg-3">
					<form action="<?= base_url('auth/process_login') ?>" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="200">
						<div class="row gy-4">
							<div class="form-group col-md-12">
								<label for="subject-field" class="pb-2">Username</label>
								<input type="text" class="form-control" name="username" id="username-field" required="">
							</div>

							<div class="form-group col-md-12">
								<label for="subject-field" class="pb-2">Password</label>
								<input type="password" class="form-control" name="password" id="password-field" required="">
							</div>

							<div class="col-md-12 text-center">
								<button type="submit">Submit</button>
							</div>

						</div>
					</form>
				</div><!-- End Contact Form -->

			</div>

		</div>

	</section><!-- /Contact Section -->

</main>