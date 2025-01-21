import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';

registerBlockType('ccf/contact-form', {
    title: 'Contact Form',
    icon: 'email',
    category: 'widgets',
    edit: () => {
        const blockProps = useBlockProps();
        return (
            <div {...blockProps}>
                <form id="ccf-contact-form" novalidate>
                    <label>
                        First Name:
                        <input type="text" name="first_name" required />
                        <span class="error-message"></span>
                    </label>
                    <label>
                        Last Name:
                        <input type="text" name="last_name" required />
                        <span class="error-message"></span>
                    </label>
                    <label>
                        Subject:
                        <input type="text" name="subject" required />
                        <span class="error-message"></span>
                    </label>
                    <label>
                        Message:
                        <textarea name="message" required></textarea>
                        <span class="error-message"></span>
                    </label>
                    <label>
                        E-mail:
                        <input type="email" name="email" required />
                        <span class="error-message"></span>
                    </label>
                    <button type="submit">Send</button>
                </form>
                <div id="ccf-message"></div>
            </div>
        );
    },
    save: () => {
        return (
            <div>
                <form id="ccf-contact-form" novalidate>
                    <label>
                        First Name:
                        <input type="text" name="first_name" required />
                        <span class="error-message"></span>
                    </label>
                    <label>
                        Last Name:
                        <input type="text" name="last_name" required />
                        <span class="error-message"></span>
                    </label>
                    <label>
                        Subject:
                        <input type="text" name="subject" required />
                        <span class="error-message"></span>
                    </label>
                    <label>
                        Message:
                        <textarea name="message" required></textarea>
                        <span class="error-message"></span>
                    </label>
                    <label>
                        E-mail:
                        <input type="email" name="email" required />
                        <span class="error-message"></span>
                    </label>
                    <button type="submit">Send</button>
                </form>
                <div id="ccf-message"></div>
            </div>
        );
    },
});
