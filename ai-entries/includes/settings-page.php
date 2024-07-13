<div class="wrap">
    <h2>AIEntries Settings</h2>
    <p>The api call returns jsons using this JSON schema : <code>{'title': str,'content':str}</code> to automate the creation of WordPress posts</p>
    <p>This plugin runs once a day according to the following parameters:</p>

    <form method="post" action="">
        <label for="question">
            <h3>Theme about the entries you want to create:</h3>
        </label>
        <input type="text" id="question" name="question" value="<?php echo esc_attr($question); ?>" required><br>
        <label for="num_calls">
            <h3>Number of posts created based on GEMINI API Call (we recommend 10 because possible errors calling the API):</h3>
        </label>
        <input type="number" id="num_calls" name="num_calls" min="1" value="<?php echo intval($num_calls); ?>" required><br>
        <label for="api_key">
            <h3>GEMINI API Key:</h3>
        </label>
        <input type="password" id="api_key" name="api_key" value="<?php echo esc_attr($api_key); ?>" required><br>
        <p>Note: You can get one for free <a target="_blank" href="https://ai.google.dev/gemini-api/docs/api-key?hl=es-419">here</a></p>
        <label for="api_key_stable_diffusion">
            <h3>Stable Diffusion API Key:</h3>
        </label>
        <input type="password" id="api_key_stable_diffusion" name="api_key_stable_diffusion" value="<?php echo esc_attr($api_key_stable_diffusion); ?>" required><br>
        <p>Note: You can get one for free <a target="_blank" href="https://stability.ai/">here</a></p>
        <label for="category">
            <h3>Category Name for the posts:</h3>
        </label>
        <input type="text" id="category" name="category" value="<?php echo esc_attr($category); ?>" required><br><br>
        <input type="submit" name="submit" value="Submit">
    </form>

    <?php if (!empty($errors)): ?>
        <h3>Errors during creation of posts: <?php echo count($errors) ?></h3>
        <p>The creation of the posts could fail due to the request made to the model API, remember that if the API key you are using is free it could generate this type of errors due to limitations with the requests.
            For more information <a target="_blank" href="https://gemini.google.com/advanced?utm_source=google&utm_medium=cpc&utm_campaign=sem_lp_sl&gad_source=1&gclid=CjwKCAjwqMO0BhA8EiwAFTLgII3-Yyyf4-LZHwQgJNtl7-LAGz9OmcyBNtUVowaQXhznCYZx3qlGCxoCyvUQAvD_BwE">click here</a></p>
        <?php foreach ($errors as $error): ?>
            <p style="color: red;">1 post create failed due to: <?php echo esc_html($error); ?></p>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($responses)): ?>
        <h3>Posts Created by GEMINI's API Call:</h3>
        <?php foreach ($responses as $response): ?>
            <pre><a href="<?php echo get_post_permalink($response->ID); ?>" target="_blank"><?php echo get_the_title($response->ID); ?></a></pre>
        <?php endforeach; ?>
    <?php endif; ?>
    <p style="color: red;"><b>DISCLAIMER: this is a work in progress. The quantity of posts created by this plugin depends on your API key limitations</b></p>
</div>
