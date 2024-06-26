<div class="search-container">
    <div class="search-wrap">
        <form action="<?php echo site_url(); ?>" class="ds-search" method="post">
            <div class="input-wrap">
                <div class="input-item">    
                    <input type="text" name="live_search" id="live_search" placeholder="We know the way...">
                    <div class="livesearch-wrap" style="display: block;">
                        <div class="livesearch-loader" style="display:none;">
                            <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                width="24px" height="30px" viewBox="0 0 24 30" style="enable-background:new 0 0 50 50;" xml:space="preserve">
                                <rect x="0" y="0" width="4" height="10" fill="#333">
                                <animateTransform attributeType="xml"
                                    attributeName="transform" type="translate"
                                    values="0 0; 0 20; 0 0"
                                    begin="0" dur="0.6s" repeatCount="indefinite" />
                                </rect>
                                <rect x="10" y="0" width="4" height="10" fill="#333">
                                <animateTransform attributeType="xml"
                                    attributeName="transform" type="translate"
                                    values="0 0; 0 20; 0 0"
                                    begin="0.2s" dur="0.6s" repeatCount="indefinite" />
                                </rect>
                                <rect x="20" y="0" width="4" height="10" fill="#333">
                                <animateTransform attributeType="xml"
                                    attributeName="transform" type="translate"
                                    values="0 0; 0 20; 0 0"
                                    begin="0.4s" dur="0.6s" repeatCount="indefinite" />
                                </rect>
                            </svg>
                        </div>  
                    </div>
                </div>
                <button type="submit" class="ds-submit">Search</button>
            </div>
        </form>
    </div>
</div>