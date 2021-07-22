<?php
/**
 * Adds the Directory Meta to both Single and Archive views
 *
 * @package TechEd_Directory_Shortcode
 * @since 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Exit if function needed to pull meta fields does not exist
if ( ! function_exists( 'teched_directory_get_field' ) ) exit;
?>
<div class="row directory-item">
    <?php if ( has_post_thumbnail() ) : ?>
        <div class="small-12 medium-4 columns">
            <div class="featured-image">
                <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_post_thumbnail( 'colormag-featured-image' ); ?></a>
            </div>
        </div>
    <?php endif; ?>
    <div class="small-12 columns<?php echo ( has_post_thumbnail() ) ? ' medium-8' : ''; ?>">

        <header class="directory-item-header">
            <h3 class="entry-title">
                <?php the_title(); ?>
            </h3>
        </header>

        <div class="directory-item-content">
        
            <?php
            $states = array();

            if ( function_exists( 'teched_directory_get_state_list' ) ) {
                $states = teched_directory_get_state_list();
            }

            $state = teched_directory_get_field( 'state' );
            ?>

            <p>
                <?php if ( $name = teched_directory_get_field( 'name' ) ) : ?>
                    <strong><?php _e( 'Name:', 'teched-directory-shortcode' ); ?></strong>
                    <?php echo ' ' . trim( $name ); ?>
                    <br />
                <?php endif; ?>

                <?php if ( $business_email = teched_directory_get_field( 'business_email' ) ) : ?>
                    <strong><?php _e( 'Email:', 'teched-directory-shortcode' ); ?></strong>
                    <a href="mailto:<?php echo trim( $business_email ); ?>">
                        <?php echo trim( $business_email ); ?>
                    </a>
                    <br />
                <?php endif; ?>

                <?php if ( $phone = teched_directory_get_field( 'phone' ) ) : ?>
                    <strong><?php _e( 'Phone:', 'teched-directory-shortcode' ); ?></strong>
                    <?php echo ' ' . trim( teched_get_phone_number_link( $phone ) ); ?>
                    <br />
                <?php endif; ?>

                <?php if ( $fax = teched_directory_get_field( 'fax' ) ) : ?>
                    <strong><?php _e( 'Fax:', 'teched-directory-shortcode' ); ?></strong>
                    <?php echo ' ' . trim( teched_get_phone_number_link( $fax ) ); ?>
                    <br />
                <?php endif; ?>
            </p>

            <p>
                <strong><?php _e( 'Address:', 'teched-directory-shortcode' ); ?></strong>
                <br />
                <?php echo ( $address_1 = teched_directory_get_field( 'street_address_1' ) ) ? $address_1 . '<br />' : ''; ?>
                <?php echo ( $address_2 = teched_directory_get_field( 'street_address_2' ) ) ? $address_2 . '<br />' : ''; ?>
                <?php echo ( $city = teched_directory_get_field( 'city' ) ) ? $city . ', ' : ''; ?>
                <?php echo ( isset( $states[ $state ] ) ) ? $states[ $state ] : $state; ?>
                <?php echo ( $zip = teched_directory_get_field( 'zip' ) ) ? ' ' . $zip : ''; ?>
            </p>

            <?php if ( $website_url = teched_directory_get_field( 'website_url' ) ) : ?>
                <p>
                    <a href="<?php echo trim( $website_url ); ?>" target="_blank">
                        <?php if ( $website_text = teched_directory_get_field( 'website_text' ) ) : ?>
                            <?php echo trim( $website_text ); ?>
                        <?php else : ?>
                            <?php echo trim( $website_url ); ?>
                        <?php endif; ?>
                    </a>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>