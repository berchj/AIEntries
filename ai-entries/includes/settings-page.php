<?php
if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
?>
<div class="wrap">
    <h2>AIEntries Setup Wizard</h2>

    <p>This plugin uses Google artificial intelligence (GEMINI) and stability.AI to automate the creation of standard WordPress posts based on configurable parameters from the WordPress admin view. It can create posts from any topic you configure from the administrator view. To ensure quality content, this tool is integrated with several free-to-use APIs to fulfill its functionality.</p>

    <h3>Freemium Features</h3>
    <ul>
        <li><strong>Basic Content Generation:</strong> Generate up to 5 posts per day using the news API.</li>
        
        <li><strong>Community Support:</strong> Access the WordPress forum for questions and support.</li>
        
        <li><strong>Access to Updates:</strong> Receive regular updates for the plugin's free version.</li>
    </ul>


    <form id="ai-entries-form" method="post" action="">
        <?php wp_nonce_field('aic_entries_settings_nonce', 'aic_entries_nonce'); ?>

        <!-- Step 1: Theme and Category -->
        <div id="step-1" class="wizard-step">
            <h3>Step 1: Theme about the entries you want to create</h3>
            <input type="text" id="question" name="question" value="<?php echo esc_attr($question); ?>" required>
            <p>Example: "Latest Tech News"</p>
            
            <label for="category"><h3>Category Name for the posts</h3></label>
            <input type="text" id="category" name="category" value="<?php echo esc_attr($category); ?>" required>
            <p>Example: "Tech News"</p>

            <button type="button" class="next-step">Next</button>
        </div>

        <!-- Step 2: Number of Posts -->
        <div id="step-2" class="wizard-step" style="display:none;">
            <h3>Step 2: Number of Posts to create</h3>
            <input type="number" id="num_calls" name="num_calls" min="1" max="5" value="<?php echo intval($num_calls); ?>" required>
            <button type="button" class="prev-step">Previous</button>
            <button type="button" class="next-step">Next</button>
        </div>

        <!-- Step 3: API Keys -->
        <div id="step-3" class="wizard-step" style="display:none;">
            <h3>Step 3: API Keys</h3>
            <label for="news_api_key">NEWSAPI API Key:</label>
            <input type="password" id="news_api_key" name="news_api_key" value="<?php echo esc_attr($news_api_key); ?>" required><br>
            <p>Get your API key <a target="_blank" href="https://newsapi.org/">here</a>.</p>

            <label for="api_key">GEMINI API Key:</label>
            <input type="password" id="api_key" name="api_key" value="<?php echo esc_attr($api_key); ?>" required><br>
            <p>Get your API key <a target="_blank" href="https://ai.google.dev/gemini-api/docs/api-key?hl=es-419">here</a>.</p>

            <button type="button" class="prev-step">Previous</button>
            <button type="button" class="next-step">Next</button>
        </div>

        <!-- Step 4: Stable Diffusion API Key -->
        <div id="step-4" class="wizard-step" style="display:none;">
            <h3>Step 4: Stable Diffusion API Key</h3>
            <label for="api_key_stable_diffusion">Stable Diffusion API Key:</label>
            <input type="password" id="api_key_stable_diffusion" name="api_key_stable_diffusion" value="<?php echo esc_attr($api_key_stable_diffusion); ?>" required><br>
            <p>Get your API key <a target="_blank" href="https://stability.ai/">here</a>.</p>

            <button type="button" class="prev-step">Previous</button>
            <input type="submit" id="submit-button" name="submit" value="Submit">
        </div>
    </form>

    <h3>Support</h3>
    <p>If you need help, please visit the support forum for this plugin:
    <a href="https://wordpress.org/support/plugin/ai-entries/" target="_blank">WordPress Support Forum</a></p>

    <h3>Contribute</h3>
    <p>If you would like to contribute to this project, please visit the GitHub repository:
    <a href="https://github.com/berchj/AIEntries" target="_blank">GitHub Repository</a></p>

    <!-- Error handling -->
    <?php if (!empty($errors)): ?>
        <h3>Errors during creation of posts: <?php echo count($errors); ?></h3>
        <p>The creation of the posts could fail due to the request made to the model API. For more information <a target="_blank" href="https://gemini.google.com/advanced?utm_source=google&utm_medium=cpc&utm_campaign=sem_lp_sl&gad_source=1&gclid=CjwKCAjwqMO0BhA8EiwAFTLgII3-Yyyf4-LZHwQgJNtl7-LAGz9OmcyBNtUVowaQXhznCYZx3qlGCxoCyvUQAvD_BwE">click here</a></p>
        <?php foreach ($errors as $error): ?>
            <p style="color: red;">1 post create failed due to: <?php echo esc_html($error); ?></p>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty(AIEntries_API::$responses)): ?>
        <h3>Posts Created by AI Entries:</h3>
        <?php foreach (AIEntries_API::$responses as $response): ?>
            <pre><a href="<?php echo esc_url(get_post_permalink($response->ID)); ?>" target="_blank"><?php echo esc_html(get_the_title($response->ID)); ?></a></pre>
        <?php endforeach; ?>
    <?php endif; ?>

    <p style="color: red;"><b>DISCLAIMER: this is a work in progress. The quantity of posts created by this plugin depends on your API key limitations</b></p>

</div>

<script>
    // JavaScript para el asistente
    document.querySelectorAll('.next-step').forEach(button => {
        button.addEventListener('click', () => {
            const currentStep = button.parentElement;
            currentStep.style.display = 'none';
            currentStep.nextElementSibling.style.display = 'block'; // Siguiente paso
        });
    });

    document.querySelectorAll('.prev-step').forEach(button => {
        button.addEventListener('click', () => {
            const currentStep = button.parentElement;
            currentStep.style.display = 'none';
            currentStep.previousElementSibling.style.display = 'block'; // Paso anterior
        });
    });
</script>
