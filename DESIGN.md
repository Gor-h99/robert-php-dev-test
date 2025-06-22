# Design and Architecture

## Multilingual Content Management

To handle multilingual content efficiently, I would adopt a **centralized translation management system**. The core idea is to have a single source of truth for all source content, with translations linked to it.

- **Source Text as Keys**: Instead of using abstract keys (e.g., `HOME_PAGE_TITLE`), the source text itself ("Welcome to our Website") would be the key. This is more intuitive for developers and translators.
- **Language-Specific Storage**: Translations would be stored in a way that is easily queryable by language. In a relational database, this could be a `translations` table with a `language_code` column. In a NoSQL database, this could be a document per language.
- **Content Delivery**: A Content Delivery Network (CDN) would be used to serve translations to the end-users, ensuring low latency.

## Design Patterns

To ensure scalability, maintainability, and flexibility, I would use the following design patterns:

- **Model-View-Controller (MVC)**: This pattern separates the application's logic from its presentation. The `TranslationUnit` class acts as the Model, the React components are the View, and the API endpoints in `translations.php` are the Controller.
- **Repository Pattern**: This pattern abstracts the data layer, making it easier to switch between different data sources (e.g., from a JSON file to a MySQL database). I would create a `TranslationRepository` class to handle all data access logic.
- **Strategy Pattern**: For version control of translations, the Strategy pattern can be used. Different versioning strategies (e.g., simple history, Git-like diffing) can be implemented as separate classes and switched at runtime.

## Database Schema

Here is a proposed database schema for storing and managing translation units using a relational database like MySQL.

**`translation_units`**

| Column          | Type         | Description                                     |
|-----------------|--------------|-------------------------------------------------|
| `id`            | `INT` (PK)   | The primary key for the translation unit.       |
| `source_text`   | `TEXT`       | The original text in the source language.       |
| `created_at`    | `TIMESTAMP`  | The timestamp when the unit was created.        |
| `updated_at`    | `TIMESTAMP`  | The timestamp when the unit was last updated.   |

**`translations`**

| Column                | Type         | Description                                     |
|-----------------------|--------------|-------------------------------------------------|
| `id`                  | `INT` (PK)   | The primary key for the translation.            |
| `unit_id`             | `INT` (FK)   | Foreign key to the `translation_units` table.   |
| `language_code`       | `VARCHAR(5)` | The language of the translation (e.g., 'en', 'fr').|
| `target_text`         | `TEXT`       | The translated text.                            |
| `created_at`          | `TIMESTAMP`  | The timestamp when the translation was created. |

**`translation_history`**

| Column                | Type         | Description                                     |
|-----------------------|--------------|-------------------------------------------------|
| `id`                  | `INT` (PK)   | The primary key for the history record.         |
| `translation_id`      | `INT` (FK)   | Foreign key to the `translations` table.        |
| `old_target_text`     | `TEXT`       | The previous translated text.                   |
| `new_target_text`     | `TEXT`       | The new translated text.                        |
| `changed_by_user_id`  | `INT` (FK)   | Foreign key to a `users` table.                 |
| `changed_at`          | `TIMESTAMP`  | The timestamp of the change.                    |

## Version Control for Translations

Version control for translations would be implemented by tracking changes to the `target_text` in the `translations` table. The `translation_history` table would store a log of these changes.

- **Simple History**: Every time a translation is updated, a new record is created in the `translation_history` table. This provides a simple audit trail.
- **Diff and Patch**: For more advanced version control, we could store diffs (differences) between versions instead of the full text. This would be more space-efficient. Libraries like `google-diff-match-patch` could be used for this.
- **User Attribution**: The `changed_by_user_id` column allows tracking who made each change, which is crucial for quality control in a collaborative translation environment. 