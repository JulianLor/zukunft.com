--
-- Database: zukunft
--

-- --------------------------------------------------------

ALTER DATABASE zukunft SET search_path TO public;

-- --------------------------------------------------------

--
-- table structure for the core configuration of this pod e.g. the program version or pod url
--

CREATE TABLE IF NOT EXISTS config
(
    config_id   BIGSERIAL PRIMARY KEY,
    config_name varchar(200) DEFAULT NULL,
    code_id     varchar(100) NOT NULL,
    value       varchar(100) DEFAULT NULL,
    description text
);

COMMENT ON TABLE config IS 'core configuration of this pod e.g. the program version or pod url';
COMMENT ON COLUMN config.config_name IS 'short name of the configuration entry to be shown to the admin';

-- --------------------------------------------------------

--
-- table structure for system log types e.g. info, warning and error
-- TODO change to an enum because this will probably never change
--

CREATE TABLE IF NOT EXISTS sys_log_types
(
    sys_log_type_id BIGSERIAL PRIMARY KEY,
    type_name       varchar(200) NOT NULL,
    code_id         varchar(50)  NOT NULL
);

COMMENT ON TABLE sys_log_types IS 'system log types e.g. info, warning and error';

--
-- table structure for table sys_log_status
--

CREATE TABLE IF NOT EXISTS sys_log_status
(
    sys_log_status_id BIGSERIAL PRIMARY KEY,
    type_name         varchar(200) NOT NULL,
    code_id           varchar(50)  NOT NULL,
    description       text         NOT NULL,
    action            varchar(200) DEFAULT NULL
);

COMMENT ON TABLE sys_log_status IS 'Status of internal errors';
COMMENT ON COLUMN sys_log_status.action IS 'description of the action to get to this status';

--
-- table structure for table sys_log_functions
--

CREATE TABLE IF NOT EXISTS sys_log_functions
(
    sys_log_function_id   BIGSERIAL PRIMARY KEY,
    sys_log_function_name varchar(200) NOT NULL
);

--
-- table structure for table sys_log
--

CREATE TABLE IF NOT EXISTS sys_log
(
    sys_log_id          BIGSERIAL PRIMARY KEY,
    sys_log_time        timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    sys_log_type_id     bigint    NOT NULL,
    sys_log_function_id bigint    NOT NULL,
    sys_log_text        text,
    sys_log_description text,
    sys_log_trace       text,
    user_id             bigint             DEFAULT NULL,
    solver_id           bigint             DEFAULT NULL,
    sys_log_status_id   bigint             DEFAULT '1'
);

COMMENT ON COLUMN sys_log.solver_id IS 'user id of the user that is trying to solve the problem';

-- --------------------------------------------------------

--
-- table structure for batch jobs that are scheduled
--

CREATE TABLE IF NOT EXISTS sys_scripts
(
    sys_script_id   BIGSERIAL PRIMARY KEY,
    sys_script_name varchar(200) NOT NULL
);

--
-- table structure for the schedule of system batch jobs
--

CREATE TABLE IF NOT EXISTS sys_script_times
(
    sys_script_time  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    sys_script_start timestamp,
    sys_script_id    bigint    NOT NULL,
    url              varchar(250)       DEFAULT NULL
);

-- --------------------------------------------------------

--
-- table structure for batch job types
--

CREATE TABLE IF NOT EXISTS calc_and_cleanup_task_types
(
    calc_and_cleanup_task_type_id BIGSERIAL PRIMARY KEY,
    type_name                     varchar(200) NOT NULL,
    description                   text,
    code_id                       varchar(50)  NOT NULL
);

COMMENT ON TABLE calc_and_cleanup_task_types IS 'batch job types e.g. data synchronisation';

--
-- table structure for batch jobs
--

CREATE TABLE IF NOT EXISTS calc_and_cleanup_tasks
(
    calc_and_cleanup_task_id      BIGSERIAL PRIMARY KEY,
    user_id                       bigint    NOT NULL,
    request_time                  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    start_time                    timestamp,
    end_time                      timestamp,
    calc_and_cleanup_task_type_id bigint    NOT NULL,
    row_id                        bigint    DEFAULT NULL,
    change_field_id               bigint    DEFAULT NULL
);

COMMENT ON TABLE calc_and_cleanup_tasks IS 'concrete batch jobs with start and end';

-- --------------------------------------------------------

--
-- table structure for table user_types
--

CREATE TABLE IF NOT EXISTS user_types
(
    user_type_id BIGSERIAL PRIMARY KEY,
    user_type    varchar(200) NOT NULL,
    code_id      varchar(100) DEFAULT NULL,
    comment      varchar(200) NOT NULL
);

--
-- table structure for table user_profiles
--

CREATE TABLE IF NOT EXISTS user_profiles
(
    profile_id  BIGSERIAL PRIMARY KEY,
    type_name   varchar(200) NOT NULL,
    code_id     varchar(50)  NOT NULL,
    description text,
    right_level smallint DEFAULT NULL
);

--
-- table structure for users including system users
--

CREATE TABLE IF NOT EXISTS users
(
    user_id                  BIGSERIAL PRIMARY KEY,
    user_name                varchar(100)          NOT NULL,
    description              text                  DEFAULT NULL,
    code_id                  varchar(50)           DEFAULT NULL,
    right_level              smallint              DEFAULT NULL,
    password                 varchar(200)          DEFAULT NULL,
    email                    varchar(200)          DEFAULT NULL,
    email_verified           smallint              DEFAULT NULL,
    email_alternative        varchar(200)          DEFAULT NULL,
    ip_address               varchar(50)           DEFAULT NULL,
    mobile_number            varchar(50)           DEFAULT NULL,
    mobile_verified          smallint              DEFAULT NULL,
    first_name               varchar(200)          DEFAULT NULL,
    last_name                varchar(200)          DEFAULT NULL,
    street                   varchar(300)          DEFAULT NULL,
    place                    varchar(200)          DEFAULT NULL,
    country_id               bigint                DEFAULT NULL,
    post_verified            smallint              DEFAULT NULL,
    official_id              varchar(200)          DEFAULT NULL,
    user_official_id_type_id bigint                DEFAULT NULL,
    official_verified        bigint                DEFAULT NULL,
    user_type_id             bigint                DEFAULT NULL,
    last_word_id             bigint                DEFAULT NULL,
    last_mask_id             bigint                DEFAULT NULL,
    is_active                smallint     NOT NULL DEFAULT '0',
    dt                       timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    last_logoff              timestamp    NULL     DEFAULT NULL,
    user_profile_id          bigint                DEFAULT NULL,
    source_id                bigint                DEFAULT NULL,
    activation_key           varchar(200)          DEFAULT NULL,
    activation_key_timeout   timestamp    NULL     DEFAULT NULL
);

COMMENT ON TABLE users IS 'only users can add data';
COMMENT ON COLUMN users.code_id IS 'to select e.g. the system batch user';
COMMENT ON COLUMN users.official_id IS 'such as the passport id';
COMMENT ON COLUMN users.last_word_id IS 'the last term that the user had used'; -- TODO rename to last_phrase_id
COMMENT ON COLUMN users.last_mask_id IS 'the last mask that the user has used';
COMMENT ON COLUMN users.source_id IS 'the last source used by this user to have a default for the next value';

--
-- table structure for table user_official_types
--

CREATE TABLE IF NOT EXISTS user_official_types
(
    user_official_type_id BIGSERIAL PRIMARY KEY,
    type_name             varchar(200) NOT NULL,
    code_id               varchar(100) DEFAULT NULL,
    comment               text         DEFAULT NULL
);

--
-- table structure for table user_requests
--

CREATE TABLE IF NOT EXISTS user_requests
(
    id          bigint      NOT NULL,
    uid         bigint      NOT NULL,
    request_key varchar(20) NOT NULL,
    expire      timestamp   NOT NULL,
    type        varchar(20) NOT NULL
);

--
-- table structure to log the user access attempts
--

CREATE TABLE IF NOT EXISTS user_attempts
(
    id          bigint      NOT NULL,
    ip          varchar(39) NOT NULL,
    expire_date timestamp   NOT NULL
);

--
-- table structure of ip addresses that should be blocked
--

CREATE TABLE IF NOT EXISTS user_blocked_ips
(
    user_blocked_id BIGSERIAL PRIMARY KEY,
    ip_from         varchar(45) NOT NULL,
    ip_to           varchar(45) NOT NULL,
    reason          text        NOT NULL,
    is_active       smallint DEFAULT '1'
);

-- --------------------------------------------------------

--
-- table structure for table sessions
--

CREATE TABLE IF NOT EXISTS sessions
(
    id          bigint       NOT NULL,
    uid         bigint       NOT NULL,
    hash        varchar(40)  NOT NULL,
    expire_date timestamp    NOT NULL,
    ip          varchar(39)  NOT NULL,
    agent       varchar(200) NOT NULL,
    cookie_crc  varchar(40)  NOT NULL
);

-- --------------------------------------------------------

--
-- table structure for add, change or delete actions
--

CREATE TABLE IF NOT EXISTS change_actions
(
    change_action_id   BIGSERIAL PRIMARY KEY,
    change_action_name varchar(200) NOT NULL,
    description        text,
    code_id            varchar(50)  NOT NULL
);

--
-- table structure to keep the original table name even if a table name has changed
--

CREATE TABLE IF NOT EXISTS change_tables
(
    change_table_id   BIGSERIAL PRIMARY KEY,
    change_table_name varchar(100) NOT NULL,
    description       varchar(1000) DEFAULT NULL,
    code_id           varchar(50)   DEFAULT NULL
);

COMMENT ON TABLE change_tables IS 'to avoid log changes in case a table is renamed';
COMMENT ON COLUMN change_tables.change_table_name IS 'the real name';
COMMENT ON COLUMN change_tables.description IS 'the user readable name';
COMMENT ON COLUMN change_tables.code_id IS 'with this field tables can be combined in case of renaming';

--
-- table structure to keep the original field name even if a table name has changed
--

CREATE TABLE IF NOT EXISTS change_fields
(
    change_field_id   BIGSERIAL PRIMARY KEY,
    change_field_name varchar(255) NOT NULL,
    table_id          bigint       NOT NULL,
    description       text,
    code_id           varchar(100) DEFAULT NULL
);

COMMENT ON COLUMN change_fields.table_id IS 'because every field must only be unique within a table';
COMMENT ON COLUMN change_fields.code_id IS 'to display the change with some linked information';

--
-- table structure to log the changes done by the users
--
-- TODO if change table gets too big, rename the table to "_up_to_YYYY_MM_DD_HH:MM:SS.000"
--      after that create a new table and start numbering from zero
--

CREATE TABLE IF NOT EXISTS changes
(
    change_id        BIGSERIAL PRIMARY KEY,
    change_time      timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_id          bigint    NOT NULL,
    change_action_id bigint    NOT NULL,
    change_field_id  bigint    NOT NULL,
    row_id           bigint             DEFAULT NULL,
    old_value        varchar(300)       DEFAULT NULL,
    new_value        varchar(300)       DEFAULT NULL,
    old_id           bigint             DEFAULT NULL,
    new_id           bigint             DEFAULT NULL
);

COMMENT ON TABLE changes IS 'to log all changes';
COMMENT ON COLUMN changes.change_time IS 'time when the value has been changed';
COMMENT ON COLUMN changes.old_id IS 'old value id';
COMMENT ON COLUMN changes.new_id IS 'new value id';

--
-- table structure to log the value changes done by the users
--

CREATE TABLE IF NOT EXISTS changes_values
(
    change_id        BIGSERIAL PRIMARY KEY,
    change_time      timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_id          bigint    NOT NULL,
    change_action_id bigint    NOT NULL,
    change_field_id  bigint    NOT NULL,
    group_id         char(112) NOT NULL,
    old_value        double precision DEFAULT NULL,
    new_value        double precision DEFAULT NULL
);

COMMENT ON TABLE changes_values IS 'to log all number changes';
COMMENT ON COLUMN changes_values.change_time IS 'time when the value has been changed';

--
-- table structure to log changes of numbers related to not more than four prime phrases
--

CREATE TABLE IF NOT EXISTS changes_values_prime
(
    change_id        BIGSERIAL PRIMARY KEY,
    change_time      timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_id          bigint    NOT NULL,
    change_action_id bigint    NOT NULL,
    change_field_id  bigint    NOT NULL,
    group_id         bigint    NOT NULL,
    old_value        double precision DEFAULT NULL,
    new_value        double precision DEFAULT NULL
);

COMMENT ON TABLE changes_values_prime IS 'to log changes of numbers related to not more than four prime phrases';
COMMENT ON COLUMN changes_values_prime.change_time IS 'time when the value has been changed';

--
-- table structure to log changes of numbers related to more than 16 phrases
--

CREATE TABLE IF NOT EXISTS changes_values_big
(
    change_id        BIGSERIAL PRIMARY KEY,
    change_time      timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_id          bigint    NOT NULL,
    change_action_id bigint    NOT NULL,
    change_field_id  bigint    NOT NULL,
    group_id         TEXT      NOT NULL,
    old_value        double precision DEFAULT NULL,
    new_value        double precision DEFAULT NULL
);

COMMENT ON TABLE changes_values_big IS 'to log changes of numbers related to more than 16 phrases';
COMMENT ON COLUMN changes_values_big.change_time IS 'time when the value has been changed';

--
-- table structure to log the link changes done by the users
--

CREATE TABLE IF NOT EXISTS change_links
(
    change_link_id   BIGSERIAL PRIMARY KEY,
    change_time      timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    user_id          bigint    NOT NULL,
    change_action_id bigint    NOT NULL,
    change_table_id  bigint    NOT NULL,
    old_from_id      bigint             DEFAULT NULL,
    old_link_id      bigint             DEFAULT NULL,
    old_to_id        bigint             DEFAULT NULL,
    old_text_from    text,
    old_text_link    text,
    old_text_to      text,
    new_from_id      bigint             DEFAULT NULL,
    new_link_id      bigint             DEFAULT NULL,
    new_to_id        bigint             DEFAULT NULL,
    new_text_from    text,
    new_text_link    text,
    new_text_to      text,
    row_id           bigint             DEFAULT NULL
);

COMMENT ON COLUMN change_links.new_to_id IS 'either internal row id or the ref type id of the external system e.g. 2 for wikidata';
COMMENT ON COLUMN change_links.new_text_to IS 'the fixed text to display to the user or the external reference id e.g. Q1 (for universe) in case of wikidata';

-- --------------------------------------------------------

--
-- table structure to add user comments related to any database entry
--

CREATE TABLE IF NOT EXISTS comments
(
    comment_id BIGSERIAL PRIMARY KEY,
    table_id   bigint NOT NULL,
    row_id     bigint NOT NULL,
    comment    text   NOT NULL
);

COMMENT ON TABLE comments IS 'separate table because it is expected that only a few record';

-- --------------------------------------------------------

--
-- table structure for table protection_types
--

CREATE TABLE IF NOT EXISTS protection_types
(
    protection_type_id BIGSERIAL PRIMARY KEY,
    type_name          varchar(200) NOT NULL,
    code_id            varchar(100) NOT NULL,
    description        text         NOT NULL
);

--
-- table structure for table share_types
--

CREATE TABLE IF NOT EXISTS share_types
(
    share_type_id BIGSERIAL PRIMARY KEY,
    type_name     varchar(200) NOT NULL,
    code_id       varchar(100) NOT NULL,
    description   text
);

COMMENT ON COLUMN share_types.type_name IS 'the name of the share type as displayed for the user';
COMMENT ON COLUMN share_types.code_id IS 'the code link';
COMMENT ON COLUMN share_types.description IS 'to explain the code action of the share type';

-- --------------------------------------------------------

--
-- table structure for table phrase_types
--

CREATE TABLE IF NOT EXISTS phrase_types
(
    phrase_type_id BIGSERIAL PRIMARY KEY,
    type_name      varchar(200) NOT NULL,
    description    text,
    code_id        varchar(100) DEFAULT NULL,
    scaling_factor bigint       DEFAULT NULL,
    word_symbol    varchar(5)   DEFAULT NULL
);

COMMENT ON COLUMN phrase_types.scaling_factor IS 'e.g. for percent the scaling factor is 100';
COMMENT ON COLUMN phrase_types.word_symbol IS 'e.g. for percent the symbol is %';

-- --------------------------------------------------------

--
-- table structure for table languages
--

CREATE TABLE IF NOT EXISTS languages
(
    language_id    BIGSERIAL PRIMARY KEY,
    language_name  varchar(200) NOT NULL,
    code_id        varchar(50)  NOT NULL,
    wikimedia_code varchar(50)  NOT NULL,
    description    text
);

--
-- table structure for table language_forms
--

CREATE TABLE IF NOT EXISTS language_forms
(
    language_form_id   BIGSERIAL PRIMARY KEY,
    language_form_name varchar(200) DEFAULT NULL,
    code_id            varchar(50)  DEFAULT NULL,
    language_id        bigint NOT NULL
);

COMMENT ON COLUMN language_forms.language_form_name IS 'type of adjustment of a term in a language e.g. plural';

-- --------------------------------------------------------

--
-- table structure for words
--

CREATE TABLE IF NOT EXISTS words
(
    word_id        BIGSERIAL PRIMARY KEY,
    user_id        bigint                DEFAULT NULL,
    word_name      varchar(200) NOT NULL,
    plural         varchar(200)          DEFAULT NULL,
    description    text                  DEFAULT NULL,
    phrase_type_id bigint                DEFAULT NULL,
    view_id        bigint                DEFAULT NULL,
    values         bigint                DEFAULT NULL,
    excluded       smallint              DEFAULT NULL,
    inactive       smallint              DEFAULT NULL,
    share_type_id  smallint              DEFAULT NULL,
    protect_id     smallint     NOT NULL DEFAULT '1'
);

COMMENT ON TABLE words IS 'for all text data that might be used to search for values, so basically all test';
COMMENT ON COLUMN words.word_id IS 'a 64 bit database key because humans will never be able to use more than a few million words';
COMMENT ON COLUMN words.user_id IS 'user_id of the user that has created the word';
COMMENT ON COLUMN words.plural IS 'to be replaced by a language form entry; TODO to be move to language forms';
COMMENT ON COLUMN words.description IS 'to be replaced by a language form entry';
COMMENT ON COLUMN words.view_id IS 'the default mask for this word';
COMMENT ON COLUMN words.values IS 'number of values linked to the word, which gives an indication of the importance';
COMMENT ON COLUMN words.excluded IS 'to deactivate to word and remove it from selection lists without losing related values';
COMMENT ON COLUMN words.inactive IS 'true if the word is not yet active e.g. because it is moved to the prime words with a 16 bit id';

--
-- table structure for user specific word changes
--

CREATE TABLE IF NOT EXISTS user_words
(
    word_id        bigint   NOT NULL,
    user_id        bigint   NOT NULL,
    language_id    bigint   NOT NULL DEFAULT 1,
    word_name      varchar(200)      DEFAULT NULL,
    plural         varchar(200)      DEFAULT NULL,
    description    text,
    phrase_type_id bigint            DEFAULT NULL,
    view_id        bigint            DEFAULT NULL,
    values         bigint            DEFAULT NULL,
    excluded       smallint          DEFAULT NULL,
    share_type_id  smallint          DEFAULT NULL,
    protect_id     smallint NOT NULL DEFAULT '1'
);

COMMENT ON TABLE user_words IS 'user specific overwrites of words';
COMMENT ON COLUMN user_words.user_id IS 'the user who has changed the standard word';

-- --------------------------------------------------------

--
-- table structure for table word_del_confirms
--
-- TODO move to batch_jobs ?
--

CREATE TABLE IF NOT EXISTS word_del_confirms
(
    word_del_request_id BIGSERIAL PRIMARY KEY,
    user_id             bigint    NOT NULL,
    confirm             timestamp NULL DEFAULT NULL,
    reject              timestamp NULL DEFAULT NULL
);

-- --------------------------------------------------------

--
-- table structure for table word_del_requests
--

CREATE TABLE IF NOT EXISTS word_del_requests
(
    word_del_request_id BIGSERIAL PRIMARY KEY,
    word_id             bigint       NOT NULL,
    word_name           varchar(200) NOT NULL,
    started             timestamp    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    canceled            timestamp    NULL     DEFAULT NULL,
    confirmed           timestamp    NULL     DEFAULT NULL,
    finished            timestamp    NULL     DEFAULT NULL,
    user_id             bigint       NOT NULL
);

COMMENT ON COLUMN word_del_requests.user_id IS 'the user who has requested the term deletion';

-- --------------------------------------------------------

--
-- table structure for table word_periods
--

CREATE TABLE IF NOT EXISTS word_periods
(
    word_id BIGSERIAL PRIMARY KEY,
    "from"  timestamp NOT NULL,
    "to"    timestamp NOT NULL
);

COMMENT ON TABLE word_periods IS 'to define the time period for time terms';

-- --------------------------------------------------------

--
-- table structure for triple predicates
--

CREATE TABLE IF NOT EXISTS verbs
(
    verb_id             BIGSERIAL PRIMARY KEY,
    verb_name           varchar(100) NOT NULL,
    code_id             varchar(255) DEFAULT NULL,
    description         text,
    condition_type      bigint       DEFAULT NULL,
    formula_name        varchar(200) DEFAULT NULL,
    name_plural_reverse varchar(200) DEFAULT NULL,
    name_plural         varchar(200) DEFAULT NULL,
    name_reverse        varchar(200) DEFAULT NULL,
    words               bigint       DEFAULT NULL
);

COMMENT ON TABLE verbs IS 'it is fixed coded how to behavior for each type is';
COMMENT ON COLUMN verbs.formula_name IS 'naming used in formulas';
COMMENT ON COLUMN verbs.name_plural_reverse IS 'english description for the reverse list, e.g. Companies are ...'; -- TODO move to language forms
COMMENT ON COLUMN verbs.words IS 'used for how many terms'; -- TODO rename to phrases

-- --------------------------------------------------------

--
-- table structure for table verb_usages
-- TODO check if still needed
--

CREATE TABLE IF NOT EXISTS verb_usages
(
    verb_usage_id BIGSERIAL PRIMARY KEY,
    verb_id       bigint NOT NULL,
    table_id      bigint NOT NULL
);

-- --------------------------------------------------------

--
-- table structure for triples
--

CREATE TABLE IF NOT EXISTS triples
(
    triple_id                BIGSERIAL PRIMARY KEY,
    user_id                  bigint            DEFAULT NULL,
    from_phrase_id           bigint   NOT NULL,
    verb_id                  bigint   NOT NULL,
    to_phrase_id             bigint   NOT NULL,
    triple_name              varchar(200)      DEFAULT NULL,
    name_given               varchar(200)      DEFAULT NULL,
    name_generated           varchar(200)      DEFAULT NULL,
    description              text,
    triple_condition_id      bigint            DEFAULT NULL,
    triple_condition_type_id bigint            DEFAULT NULL,
    phrase_type_id           bigint            DEFAULT NULL,
    view_id                  bigint            DEFAULT NULL,
    values                   bigint            DEFAULT NULL,
    excluded                 smallint          DEFAULT NULL,
    share_type_id            smallint          DEFAULT NULL,
    protect_id               smallint NOT NULL DEFAULT '1'
);

COMMENT ON COLUMN triples.triple_condition_id IS 'formula_id of a formula with a boolean result; the term is only added if formula result is true';
COMMENT ON COLUMN triples.triple_condition_type_id IS 'maybe not needed';
COMMENT ON COLUMN triples.name_given IS 'the unique name manually set by the user, which can be empty';
COMMENT ON COLUMN triples.name_generated IS 'the generic unique name based on the phrases and verb, which can be overwritten by the given name';
COMMENT ON COLUMN triples.description IS 'as for all other named objects an explaining text that is shown to the user in case of mouseover';

--
-- table structure for table user_triples
--

CREATE TABLE IF NOT EXISTS user_triples
(
    triple_id       BIGSERIAL PRIMARY KEY,
    user_id         bigint            DEFAULT NULL,
    language_id     bigint   NOT NULL DEFAULT 1,
    triple_name     varchar(200)      DEFAULT NULL,
    name_given      varchar(200)      DEFAULT NULL,
    name_generated  varchar(200)      DEFAULT NULL,
    description     text,
    phrase_type_id  bigint            DEFAULT NULL,
    view_id         bigint            DEFAULT NULL,
    values          bigint            DEFAULT NULL,
    excluded        smallint          DEFAULT NULL,
    share_type_id   smallint          DEFAULT NULL,
    protect_id      smallint NOT NULL DEFAULT '1'
);

COMMENT ON COLUMN user_triples.name_given IS 'the unique name manually set by the user, which can be empty';
COMMENT ON COLUMN user_triples.name_generated IS 'the generic unique name based on the phrases and verb, which can be overwritten by the given name';

-- --------------------------------------------------------

--
-- table structure to remember which phrases are stored in which table and pod
--

CREATE TABLE IF NOT EXISTS phrase_tables
(
    table_id   BIGSERIAL PRIMARY KEY,
    phrase_id  bigint   NOT NULL,
    pod_url    text     NOT NULL,
    active     smallint DEFAULT NULL
);

COMMENT ON TABLE phrase_tables IS 'to remember which phrases are stored in which table and pod';

-- --------------------------------------------------------

--
-- table structure to add a user given name using a 512 bit group id index for up to 16 16 bit phrase ids including the order
--

CREATE TABLE IF NOT EXISTS groups
(
    group_id    char(112) PRIMARY KEY,
    group_name  varchar(1000) DEFAULT NULL,
    description varchar(4000) DEFAULT NULL
);

COMMENT ON TABLE groups IS 'to add a user given name using a 512 bit group id index for up to 16 16 bit phrase ids including the order';
COMMENT ON COLUMN groups.group_name IS 'the name given by a user to display the group (does not need to be unique))';
COMMENT ON COLUMN groups.description IS 'the description of the group given by a user';

--
-- table structure for saving a user specific group name
--

CREATE TABLE IF NOT EXISTS user_groups
(
    group_id    char(112) PRIMARY KEY,
    user_id     bigint NOT NULL,
    group_name  varchar(1000) DEFAULT NULL,
    description varchar(4000) DEFAULT NULL
);

COMMENT ON TABLE user_groups IS 'to link the user specific name to the standard group';
COMMENT ON COLUMN user_groups.group_name IS 'the user specific group name which can contain the phrase names in a different order';
COMMENT ON COLUMN user_groups.description IS 'the user specific description for mouse over helps';

--
-- table structure for phrase group names of up to four prime phrases
--

CREATE TABLE IF NOT EXISTS groups_prime
(
    group_id    BIGSERIAL PRIMARY KEY,
    group_name  varchar(1000) DEFAULT NULL,
    description varchar(4000) DEFAULT NULL
);

COMMENT ON TABLE groups_prime IS 'to add a user given name using a 64 bit bigint group id index for up to four 16 bit phrase ids including the order';
COMMENT ON COLUMN groups_prime.group_name IS 'the name given by a user to display the group (does not need to be unique))';
COMMENT ON COLUMN groups_prime.description IS 'the description of the group given by a user';

--
-- table structure for saving a user specific group name for up to four prime phrases
--

CREATE TABLE IF NOT EXISTS user_groups_prime
(
    group_id    BIGSERIAL PRIMARY KEY,
    user_id     bigint NOT NULL,
    group_name  varchar(1000) DEFAULT NULL,
    description varchar(4000) DEFAULT NULL
);

COMMENT ON TABLE user_groups_prime IS 'to link the user specific name to the group';
COMMENT ON COLUMN user_groups_prime.group_name IS 'the user specific group name which can contain the phrase names in a different order';
COMMENT ON COLUMN user_groups_prime.description IS 'the user specific description for mouse over helps';

--
-- table structure for phrase group names of more than 16 phrases
--

CREATE TABLE IF NOT EXISTS groups_big
(
    group_id    text PRIMARY KEY,
    group_name  varchar(1000) DEFAULT NULL,
    description varchar(4000) DEFAULT NULL
);

COMMENT ON TABLE groups_big IS 'to add a user given name using text group id index for an almost unlimited number of phrase ids including the order';
COMMENT ON COLUMN groups_big.group_name IS 'the name given by a user to display the group (does not need to be unique))';
COMMENT ON COLUMN groups_big.description IS 'the description of the group given by a user';

--
-- table structure for saving a user specific group name for more than 16 phrases
--

CREATE TABLE IF NOT EXISTS user_groups_big
(
    group_id    text PRIMARY KEY,
    user_id     bigint NOT NULL,
    group_name  varchar(1000) DEFAULT NULL,
    description varchar(4000) DEFAULT NULL
);

COMMENT ON TABLE user_groups_big IS 'to link the user specific name to the group';
COMMENT ON COLUMN user_groups_big.group_name IS 'the user specific group name which can contain the phrase names in a different order';
COMMENT ON COLUMN user_groups_big.description IS 'the user specific description for mouse over helps';

-- --------------------------------------------------------

--
-- table structure to link phrases to a group
-- TODO deprecate and use like on group_id instead
--

CREATE TABLE IF NOT EXISTS group_links
(
    group_id  char(112) NOT NULL,
    phrase_id bigint NOT NULL
);

COMMENT ON TABLE group_links IS 'link phrases to a phrase group for database based selections';

--
-- table structure to store user specific ex- or includes of single link of phrases to groups
--

CREATE TABLE IF NOT EXISTS user_group_links
(
    group_id  char(112) NOT NULL,
    phrase_id bigint    NOT NULL,
    user_id   bigint    DEFAULT NULL,
    excluded  smallint  DEFAULT NULL
);

COMMENT ON TABLE user_group_links IS 'to ex- or include user specific link to the standard group';

--
-- table structure to link up to four prime phrases to a group
-- TODO deprecate and use like on binary format of group_id instead
--

CREATE TABLE IF NOT EXISTS group_prime_links
(
    group_id  BIGSERIAL,
    phrase_id bigint NOT NULL
);

COMMENT ON TABLE group_prime_links IS 'link phrases to a short phrase group for database based selections';

--
-- table structure for user specific links of up to four prime phrases per group
--

CREATE TABLE IF NOT EXISTS user_group_prime_links
(
    group_id  BIGSERIAL,
    phrase_id bigint    NOT NULL,
    user_id   bigint    DEFAULT NULL,
    excluded  smallint  DEFAULT NULL
);

COMMENT ON TABLE user_group_prime_links IS 'user specific link to groups with up to four prime phrase';

--
-- table structure to link up more than 16 phrases to a group
--

CREATE TABLE IF NOT EXISTS group_big_links
(
    group_id  text,
    phrase_id bigint NOT NULL
);

COMMENT ON TABLE group_big_links IS 'link phrases to a long phrase group for database based selections';

--
-- table structure for user specific links for more than 16 phrases per group
--

CREATE TABLE IF NOT EXISTS user_group_big_links
(
    group_id  text,
    phrase_id bigint   NOT NULL,
    user_id   bigint   DEFAULT NULL,
    excluded  smallint DEFAULT NULL
);

COMMENT ON TABLE user_group_big_links IS 'to ex- or include user specific link to the standard group';

-- --------------------------------------------------------

--
-- table structure for table source_types
--

CREATE TABLE IF NOT EXISTS source_types
(
    source_type_id BIGSERIAL PRIMARY KEY,
    type_name      varchar(200) NOT NULL,
    code_id        varchar(100) NOT NULL,
    description    text     DEFAULT NULL
);

--
-- table structure for the original sources for the numeric, time and geo values
--

CREATE TABLE IF NOT EXISTS sources
(
    source_id      BIGSERIAL    PRIMARY KEY,
    user_id        bigint       DEFAULT NULL,
    source_name    varchar(255)     NOT NULL,
    description    text         DEFAULT NULL,
    source_type_id bigint       DEFAULT NULL,
    url            text         DEFAULT NULL,
    code_id        varchar(100) DEFAULT NULL,
    excluded       smallint     DEFAULT NULL,
    share_type_id  smallint     DEFAULT NULL,
    protect_id     smallint     DEFAULT NULL
);

COMMENT ON TABLE sources                 IS 'for the original sources for the numeric, time and geo values';
COMMENT ON COLUMN sources.source_id      IS 'the internal unique primary index ';
COMMENT ON COLUMN sources.user_id        IS 'the owner / creator of the value';
COMMENT ON COLUMN sources.source_name    IS 'the unique name of the source used e.g. as the primary search key';
COMMENT ON COLUMN sources.description    IS 'the user specific description of the source for mouse over helps';
COMMENT ON COLUMN sources.source_type_id IS 'link to the source type';
COMMENT ON COLUMN sources.url            IS 'the url of the source';
COMMENT ON COLUMN sources.code_id        IS 'to select sources used by this program';
COMMENT ON COLUMN sources.excluded       IS 'true if a user, but not all, have removed it';
COMMENT ON COLUMN sources.share_type_id  IS 'to restrict the access';
COMMENT ON COLUMN sources.protect_id     IS 'to protect against unwanted changes';

--
-- table structure for the original sources for the numeric, time and geo values
--

CREATE TABLE IF NOT EXISTS user_sources
(
    source_id      bigint           NOT NULL,
    user_id        bigint           NOT NULL,
    source_name    varchar(255)     NOT NULL,
    description    text         DEFAULT NULL,
    source_type_id bigint       DEFAULT NULL,
    url            text         DEFAULT NULL,
    code_id        varchar(100) DEFAULT NULL,
    excluded       smallint     DEFAULT NULL,
    share_type_id  smallint     DEFAULT NULL,
    protect_id     smallint     DEFAULT NULL
);

COMMENT ON TABLE user_sources                 IS 'for the original sources for the numeric, time and geo values';
COMMENT ON COLUMN user_sources.source_id      IS 'with the user_id the internal unique primary index ';
COMMENT ON COLUMN user_sources.user_id        IS 'the changer of the ';
COMMENT ON COLUMN user_sources.source_name    IS 'the unique name of the source used e.g. as the primary search key';
COMMENT ON COLUMN user_sources.description    IS 'the user specific description of the source for mouse over helps';
COMMENT ON COLUMN user_sources.source_type_id IS 'link to the source type';
COMMENT ON COLUMN user_sources.url            IS 'the url of the source';
COMMENT ON COLUMN user_sources.code_id        IS 'to select sources used by this program';
COMMENT ON COLUMN user_sources.excluded       IS 'true if a user, but not all, have removed it';
COMMENT ON COLUMN user_sources.share_type_id  IS 'to restrict the access';
COMMENT ON COLUMN user_sources.protect_id     IS 'to protect against unwanted changes';


--
-- table structure for table source_values
--

CREATE TABLE IF NOT EXISTS source_values
(
    group_id     BIGSERIAL PRIMARY KEY,
    source_id    bigint           NOT NULL,
    user_id      bigint           NOT NULL,
    source_value double precision NOT NULL
);

COMMENT ON TABLE source_values IS 'one user can add different value, which should be the same, but are different';

--
-- table structure for table import_source
--
-- TODO review
--

CREATE TABLE IF NOT EXISTS import_source
(
    import_source_id BIGSERIAL PRIMARY KEY,
    name             varchar(100) NOT NULL,
    import_type      bigint       NOT NULL,
    word_id          bigint       NOT NULL
);

COMMENT ON TABLE import_source IS 'many replace by a term';
COMMENT ON COLUMN import_source.word_id IS 'the name as a term';

--
-- table structure for table source_api
-- TODO check if used and needed
--

CREATE TABLE IF NOT EXISTS source_api
(
    source_api_id       BIGSERIAL PRIMARY KEY,
    source_api_name     varchar(200) NOT NULL,
    open_api_definition text DEFAULT NULL
);

--
-- table structure for table source_api_user
-- TODO check if used and needed
--

CREATE TABLE IF NOT EXISTS source_api_user
(
    source_api_id BIGSERIAL,
    user_id       BIGSERIAL,
    source_api_     varchar(200) NOT NULL,
    open_api_definition text DEFAULT NULL
);

-- --------------------------------------------------------

--
-- table structure for table ref_types
--

CREATE TABLE IF NOT EXISTS ref_types
(
    ref_type_id BIGSERIAL PRIMARY KEY,
    type_name   varchar(200) NOT NULL,
    code_id     varchar(100) NOT NULL,
    description text         NOT NULL,
    base_url    text         NOT NULL
);

--
-- table structure for table refs
--

CREATE TABLE IF NOT EXISTS refs
(
    ref_id       BIGSERIAL PRIMARY KEY,
    user_id      bigint       DEFAULT NULL,
    phrase_id    bigint       NOT NULL,
    external_key varchar(250) NOT NULL,
    ref_type_id  bigint       NOT NULL,
    source_id    bigint       DEFAULT NULL,
    url          text         DEFAULT NULL,
    description  text         DEFAULT NULL,
    excluded     smallint     DEFAULT NULL
);

--
-- table structure for table user_refs
--

CREATE TABLE IF NOT EXISTS user_refs
(
    ref_id         bigint NOT NULL,
    user_id        bigint NOT NULL,
    url            text         DEFAULT NULL,
    description    text         DEFAULT NULL,
    excluded       smallint     DEFAULT NULL
);

-- --------------------------------------------------------

--
-- table structure for public unprotected values related up to four prime phrase that have never changed the owner, does not have a description and are rarely updated
--

CREATE TABLE IF NOT EXISTS values_standard_prime
(
    group_id      BIGSERIAL        PRIMARY KEY,
    numeric_value double precision NOT NULL,
    source_id     bigint           DEFAULT NULL
);

COMMENT ON TABLE values_standard_prime                IS 'for public unprotected values related up to four prime phrase that have never changed the owner, does not have a description and are rarely updated';
COMMENT ON COLUMN values_standard_prime.group_id      IS 'the 64-bit prime index to find the value';
COMMENT ON COLUMN values_standard_prime.numeric_value IS 'the numeric value given by the user';
COMMENT ON COLUMN values_standard_prime.source_id     IS 'the source of the value as given by the user';

--
-- table structure for public unprotected values that have never changed the owner, does not have a description and are rarely updated
--

CREATE TABLE IF NOT EXISTS values_standard
(
    group_id      char(112)        PRIMARY KEY,
    numeric_value double precision NOT NULL,
    source_id     bigint           DEFAULT NULL
);

COMMENT ON TABLE values_standard                IS 'for public unprotected values that have never changed the owner, does not have a description and are rarely updated';
COMMENT ON COLUMN values_standard.group_id      IS 'the 512-bit prime index to find the value';
COMMENT ON COLUMN values_standard.numeric_value IS 'the numeric value given by the user';
COMMENT ON COLUMN values_standard.source_id     IS 'the source of the value as given by the user';

-- --------------------------------------------------------

--
-- table structure for numeric values related to up to 16 phrases
--

CREATE TABLE IF NOT EXISTS values
(
    group_id        char(112)        PRIMARY KEY,
    numeric_value   double precision NOT NULL,
    source_id       bigint           DEFAULT NULL,
    last_update     timestamp        DEFAULT NULL,
    user_id         bigint           DEFAULT NULL,
    excluded        smallint         DEFAULT NULL,
    share_type_id   smallint         DEFAULT NULL,
    protect_id      smallint         DEFAULT NULL
);

COMMENT ON TABLE values                IS 'for numeric values related to up to 16 phrases';
COMMENT ON COLUMN values.group_id      IS 'the 512-bit prime index to find the value';
COMMENT ON COLUMN values.numeric_value IS 'the numeric value given by the user';
COMMENT ON COLUMN values.source_id     IS 'the source of the value as given by the user';
COMMENT ON COLUMN values.last_update   IS 'timestamp of the last update used also to trigger updates of depending values for fast recalculation for fast recalculation';
COMMENT ON COLUMN values.user_id       IS 'the owner / creator of the value';
COMMENT ON COLUMN values.excluded      IS 'true if a user, but not all, have removed it';
COMMENT ON COLUMN values.share_type_id IS 'to restrict the access';
COMMENT ON COLUMN values.protect_id    IS 'to protect against unwanted changes';

--
-- table structure for user specific changes of values related to up to 16 phrases
--

CREATE TABLE IF NOT EXISTS user_values
(
    group_id      char(112)        NOT NULL,
    user_id       bigint           NOT NULL,
    numeric_value double precision DEFAULT NULL,
    source_id     bigint           DEFAULT NULL,
    last_update   timestamp        DEFAULT NULL,
    excluded      smallint         DEFAULT NULL,
    share_type_id smallint         DEFAULT NULL,
    protect_id    smallint         DEFAULT NULL
);

COMMENT ON TABLE user_values                IS 'for user specific changes of values related to up to 16 phrases';
COMMENT ON COLUMN user_values.group_id      IS 'the 512-bit prime index to find the user numeric value';
COMMENT ON COLUMN user_values.user_id       IS 'the changer of the value';
COMMENT ON COLUMN user_values.numeric_value IS 'the user specific numeric value change';
COMMENT ON COLUMN user_values.source_id     IS 'the source of the value as given by the user';
COMMENT ON COLUMN user_values.last_update   IS 'timestamp of the last update used also to trigger updates of depending values for fast recalculation for fast recalculation';
COMMENT ON COLUMN user_values.excluded      IS 'true if a user, but not all, have removed it';
COMMENT ON COLUMN user_values.share_type_id IS 'to restrict the access';
COMMENT ON COLUMN user_values.protect_id    IS 'to protect against unwanted changes';

-- --------------------------------------------------------

--
-- table structure for the most often requested values related up to four prime phrase
--

CREATE TABLE IF NOT EXISTS values_prime
(
    group_id        BIGSERIAL        PRIMARY KEY,
    numeric_value   double precision NOT NULL,
    source_id       bigint           DEFAULT NULL,
    last_update     timestamp        DEFAULT NULL,
    user_id         bigint           DEFAULT NULL,
    excluded        smallint         DEFAULT NULL,
    share_type_id   smallint         DEFAULT NULL,
    protect_id      smallint         DEFAULT NULL
);

COMMENT ON TABLE values_prime                IS 'for the most often requested values related up to four prime phrase';
COMMENT ON COLUMN values_prime.group_id      IS 'the 64-bit prime index to find the value';
COMMENT ON COLUMN values_prime.numeric_value IS 'the numeric value given by the user';
COMMENT ON COLUMN values_prime.source_id     IS 'the source of the value as given by the user';
COMMENT ON COLUMN values_prime.last_update   IS 'timestamp of the last update used also to trigger updates of depending values for fast recalculation for fast recalculation';
COMMENT ON COLUMN values_prime.user_id       IS 'the owner / creator of the value';
COMMENT ON COLUMN values_prime.excluded      IS 'true if a user, but not all, have removed it';
COMMENT ON COLUMN values_prime.share_type_id IS 'to restrict the access';
COMMENT ON COLUMN values_prime.protect_id    IS 'to protect against unwanted changes';

--
-- table structure to store the user specific changes for the most often requested values related up to four prime phrase
--

CREATE TABLE IF NOT EXISTS user_values_prime
(
    group_id        bigint           NOT NULL,
    user_id         bigint           NOT NULL,
    numeric_value   double precision DEFAULT NULL,
    source_id       bigint           DEFAULT NULL,
    last_update     timestamp        DEFAULT NULL,
    excluded        smallint         DEFAULT NULL,
    share_type_id   smallint         DEFAULT NULL,
    protect_id      smallint         DEFAULT NULL
);

COMMENT ON TABLE user_values_prime                IS 'to store the user specific changes for the most often requested values related up to four prime phrase';
COMMENT ON COLUMN user_values_prime.group_id      IS 'the 64-bit prime index to find the user numeric value';
COMMENT ON COLUMN user_values_prime.user_id       IS 'the changer of the value';
COMMENT ON COLUMN user_values_prime.numeric_value IS 'the user specific numeric value change';
COMMENT ON COLUMN user_values_prime.source_id     IS 'the source of the value as given by the user';
COMMENT ON COLUMN user_values_prime.last_update   IS 'timestamp of the last update used also to trigger updates of depending values for fast recalculation for fast recalculation';
COMMENT ON COLUMN user_values_prime.excluded      IS 'true if a user, but not all, have removed it';
COMMENT ON COLUMN user_values_prime.share_type_id IS 'to restrict the access';
COMMENT ON COLUMN user_values_prime.protect_id    IS 'to protect against unwanted changes';

-- --------------------------------------------------------

--
-- table structure for values related to more than 16 phrases
--

CREATE TABLE IF NOT EXISTS values_big
(
    group_id        text             PRIMARY KEY,
    numeric_value   double precision NOT NULL,
    source_id       bigint           DEFAULT NULL,
    last_update     timestamp        DEFAULT NULL,
    user_id         bigint           DEFAULT NULL,
    excluded        smallint         DEFAULT NULL,
    share_type_id   smallint         DEFAULT NULL,
    protect_id      smallint         DEFAULT NULL
);

COMMENT ON TABLE values_big                IS 'for values related to more than 16 phrases';
COMMENT ON COLUMN values_big.group_id      IS 'the text index to find value related to more than 16 phrases';
COMMENT ON COLUMN values_big.numeric_value IS 'the numeric value given by the user';
COMMENT ON COLUMN values_big.source_id     IS 'the source of the value as given by the user';
COMMENT ON COLUMN values_big.last_update   IS 'timestamp of the last update used also to trigger updates of depending values for fast recalculation for fast recalculation';
COMMENT ON COLUMN values_big.user_id       IS 'the owner / creator of the value';
COMMENT ON COLUMN values_big.excluded      IS 'true if a user, but not all, have removed it';
COMMENT ON COLUMN values_big.share_type_id IS 'to restrict the access';
COMMENT ON COLUMN values_big.protect_id    IS 'to protect against unwanted changes';

--
-- table structure to store the user specific changes of numeric values related to more than 16 phrases
--

CREATE TABLE IF NOT EXISTS user_values_big
(
    group_id        text             NOT NULL,
    user_id         bigint           NOT NULL,
    numeric_value   double precision DEFAULT NULL,
    source_id       bigint           DEFAULT NULL,
    last_update     timestamp        DEFAULT NULL,
    excluded        smallint         DEFAULT NULL,
    share_type_id   smallint         DEFAULT NULL,
    protect_id      smallint         DEFAULT NULL
);

COMMENT ON TABLE user_values_big                IS 'to store the user specific changes of numeric values related to more than 16 phrases';
COMMENT ON COLUMN user_values_big.group_id      IS 'the text index to find the user values related to more than 16 phrases';
COMMENT ON COLUMN user_values_big.user_id       IS 'the changer of the value';
COMMENT ON COLUMN user_values_big.numeric_value IS 'the user specific numeric value change';
COMMENT ON COLUMN user_values_big.source_id     IS 'the source of the value as given by the user';
COMMENT ON COLUMN user_values_big.last_update   IS 'timestamp of the last update used also to trigger updates of depending values for fast recalculation for fast recalculation';
COMMENT ON COLUMN user_values_big.excluded      IS 'true if a user, but not all, have removed it';
COMMENT ON COLUMN user_values_big.share_type_id IS 'to restrict the access';
COMMENT ON COLUMN user_values_big.protect_id    IS 'to protect against unwanted changes';

-- --------------------------------------------------------

--
-- table structure for public text values related up to four prime phrase that have never changed the owner, does not have a description and are rarely updated
--

CREATE TABLE IF NOT EXISTS values_text_standard_prime
(
    group_id   BIGSERIAL PRIMARY KEY,
    text_value text NOT NULL,
    source_id  int DEFAULT NULL
);

COMMENT ON TABLE values_text_standard_prime IS 'for public unprotected text values related up to four prime phrase that have never changed the owner, does not have a description and are rarely updated';
COMMENT ON COLUMN values_text_standard_prime.group_id IS 'the prime index to find the value';

--
-- table structure for public text values that have never changed the owner, does not have a description and are rarely updated
--

CREATE TABLE IF NOT EXISTS value_text_standard
(
    group_id   char(112) PRIMARY KEY,
    text_value text NOT NULL,
    source_id  bigint DEFAULT NULL
);

COMMENT ON TABLE value_text_standard IS 'for public unprotected text values that have never changed the owner, does not have a description and are rarely updated';
COMMENT ON COLUMN value_text_standard.group_id IS 'the prime index to find the value';

--
-- table structure for text values where the text might be long and where the text is expected to be never user in a search
--

CREATE TABLE IF NOT EXISTS value_text
(
    group_id        char(112) PRIMARY KEY,
    text_value      text NOT NULL,
    user_id         bigint                    DEFAULT NULL,
    source_id       bigint                    DEFAULT NULL,
    description     text,
    excluded        smallint                  DEFAULT NULL,
    last_update     timestamp        NULL     DEFAULT NULL,
    share_type_id   smallint                  DEFAULT NULL,
    protect_id      smallint           NOT NULL DEFAULT '1'
);

COMMENT ON TABLE value_text IS 'for the most often used text values';
COMMENT ON COLUMN value_text.group_id IS 'the prime index to find the values';
COMMENT ON COLUMN value_text.user_id IS 'the owner / creator of the value';
COMMENT ON COLUMN value_text.last_update IS 'for fast recalculation';
COMMENT ON COLUMN value_text.description IS 'temp field used during dev phase for easy value to trm assigns';
COMMENT ON COLUMN value_text.excluded IS 'the default exclude setting for most users';

--
-- table structure to store the user specific changes of text values where the text might be long and where the text is expected to be never user in a search
--

CREATE TABLE IF NOT EXISTS user_value_text
(
    group_id        char(112) NOT NULL,
    user_id         bigint                    DEFAULT NULL,
    text_value      text NOT NULL,
    source_id       bigint                    DEFAULT NULL,
    description     text,
    excluded        smallint                  DEFAULT NULL,
    last_update     timestamp        NULL     DEFAULT NULL,
    share_type_id   smallint                  DEFAULT NULL,
    protect_id      smallint           NOT NULL DEFAULT '1'
);

COMMENT ON TABLE user_value_text IS 'to store the user specific changes of the most often used text values';
COMMENT ON COLUMN user_value_text.group_id IS 'the prime index to find the values';
COMMENT ON COLUMN user_value_text.user_id IS 'the owner / creator of the value';
COMMENT ON COLUMN user_value_text.last_update IS 'for fast recalculation';
COMMENT ON COLUMN user_value_text.description IS 'temp field used during dev phase for easy value to trm assigns';
COMMENT ON COLUMN user_value_text.excluded IS 'the default exclude setting for most users';

--
-- table structure for the most often requested text values related up to four prime phrase
--

CREATE TABLE IF NOT EXISTS value_text_prime
(
    group_id        BIGSERIAL PRIMARY KEY,
    user_id         bigint                    DEFAULT NULL,
    text_value      text NOT NULL,
    source_id       bigint                    DEFAULT NULL,
    description     text,
    excluded        smallint                  DEFAULT NULL,
    share_type_id   smallint                  DEFAULT NULL,
    protect_id      smallint           NOT NULL DEFAULT '1'
);

COMMENT ON TABLE value_text_prime IS 'for the most often used values';
COMMENT ON COLUMN value_text_prime.group_id IS 'temp field to increase speed created by the value term links';
COMMENT ON COLUMN value_text_prime.user_id IS 'the owner / creator of the value';
COMMENT ON COLUMN value_text_prime.description IS 'temp field used during dev phase for easy value to trm assigns';
COMMENT ON COLUMN value_text_prime.excluded IS 'the default exclude setting for most users';

--
-- table structure to store the user specific changes for the most often requested values related up to four prime phrase
--

CREATE TABLE IF NOT EXISTS user_value_text_prime
(
    group_id        BIGSERIAL NOT NULL,
    user_id         bigint                    DEFAULT NULL,
    text_value      text NOT NULL,
    source_id       bigint                    DEFAULT NULL,
    description     text,
    excluded        smallint                  DEFAULT NULL,
    share_type_id   smallint                  DEFAULT NULL,
    protect_id      smallint           NOT NULL DEFAULT '1'
);

COMMENT ON TABLE user_value_text_prime IS 'the user specific changes of the most often used values';
COMMENT ON COLUMN user_value_text_prime.group_id IS 'temp field to increase speed created by the value term links';
COMMENT ON COLUMN user_value_text_prime.user_id IS 'the owner / creator of the value';
COMMENT ON COLUMN user_value_text_prime.description IS 'temp field used during dev phase for easy value to trm assigns';
COMMENT ON COLUMN user_value_text_prime.excluded IS 'the default exclude setting for most users';

--
-- table structure for the most often requested text values related up to more than 16 phrases
--

CREATE TABLE IF NOT EXISTS value_text_big
(
    group_id        TEXT NOT NULL,
    user_id         bigint                    DEFAULT NULL,
    text_value      text NOT NULL,
    source_id       bigint                    DEFAULT NULL,
    description     text,
    excluded        smallint                  DEFAULT NULL,
    share_type_id   smallint                  DEFAULT NULL,
    protect_id      smallint           NOT NULL DEFAULT '1'
);

COMMENT ON TABLE value_text_big IS 'for the most often used values';
COMMENT ON COLUMN value_text_big.group_id IS 'the prime index to find the values';
COMMENT ON COLUMN value_text_big.user_id IS 'the owner / creator of the value';
COMMENT ON COLUMN value_text_big.description IS 'temp field used during dev phase for easy value to trm assigns';
COMMENT ON COLUMN value_text_big.excluded IS 'the default exclude setting for most users';

--
-- table structure to store the user specific changes for the most often requested values related up to more than 16 phrases
--

CREATE TABLE IF NOT EXISTS user_value_text_big
(
    group_id        TEXT NOT NULL,
    user_id         bigint                    DEFAULT NULL,
    text_value      text NOT NULL,
    source_id       bigint                    DEFAULT NULL,
    description     text,
    excluded        smallint                  DEFAULT NULL,
    share_type_id   smallint                  DEFAULT NULL,
    protect_id      smallint           NOT NULL DEFAULT '1'
);

COMMENT ON TABLE user_value_text_big IS 'the user specific changes of the most often used values';
COMMENT ON COLUMN user_value_text_big.group_id IS 'the prime index to find the values';
COMMENT ON COLUMN user_value_text_big.user_id IS 'the owner / creator of the value';
COMMENT ON COLUMN user_value_text_big.description IS 'temp field used during dev phase for easy value to trm assigns';
COMMENT ON COLUMN user_value_text_big.excluded IS 'the default exclude setting for most users';

-- --------------------------------------------------------

--
-- table structure for public time values related up to four prime phrase that have never changed the owner, does not have a description and are rarely updated
--

CREATE TABLE IF NOT EXISTS value_time_standard_prime
(
    group_id   BIGSERIAL PRIMARY KEY,
    time_value timestamp NOT NULL,
    source_id  int DEFAULT NULL
);

COMMENT ON TABLE value_time_standard_prime IS 'for public unprotected time values related up to four prime phrase that have never changed the owner, does not have a description and are rarely updated';
COMMENT ON COLUMN value_time_standard_prime.group_id IS 'the prime index to find the value';

--
-- table structure for public time values that have never changed the owner, does not have a description and are rarely updated
--

CREATE TABLE IF NOT EXISTS value_time_standard
(
    group_id   char(112) PRIMARY KEY,
    time_value timestamp NOT NULL,
    source_id  bigint DEFAULT NULL
);

COMMENT ON TABLE value_time_standard IS 'for public unprotected time values that have never changed the owner, does not have a description and are rarely updated';
COMMENT ON COLUMN value_time_standard.group_id IS 'the prime index to find the value';

--
-- table structure for time values where the time is expected to be never user in a search
--

CREATE TABLE IF NOT EXISTS value_time
(
    group_id        char(112) PRIMARY KEY,
    time_value      timestamp NOT NULL,
    user_id         bigint                    DEFAULT NULL,
    source_id       bigint                    DEFAULT NULL,
    description     text,
    excluded        smallint                  DEFAULT NULL,
    last_update     timestamp        NULL     DEFAULT NULL,
    share_type_id   smallint                  DEFAULT NULL,
    protect_id      smallint            NOT NULL DEFAULT '1'
);

COMMENT ON TABLE value_time IS 'for the most often used time values';
COMMENT ON COLUMN value_time.group_id IS 'the prime index to find the values';
COMMENT ON COLUMN value_time.user_id IS 'the owner / creator of the value';
COMMENT ON COLUMN value_time.last_update IS 'for fast recalculation';
COMMENT ON COLUMN value_time.description IS 'temp field used during dev phase for easy value to trm assigns';
COMMENT ON COLUMN value_time.excluded IS 'the default exclude setting for most users';

--
-- table structure to store the user specific changes of time values where the time is expected to be never user in a search
--

CREATE TABLE IF NOT EXISTS user_value_time
(
    group_id        char(112) NOT NULL,
    user_id         bigint                    DEFAULT NULL,
    time_value      timestamp NOT NULL,
    source_id       bigint                    DEFAULT NULL,
    description     text,
    excluded        smallint                  DEFAULT NULL,
    last_update     timestamp        NULL     DEFAULT NULL,
    share_type_id   smallint                  DEFAULT NULL,
    protect_id      smallint            NOT NULL DEFAULT '1'
);

COMMENT ON TABLE user_value_time IS 'to store the user specific changes of the most often used time values';
COMMENT ON COLUMN user_value_time.group_id IS 'the prime index to find the values';
COMMENT ON COLUMN user_value_time.user_id IS 'the owner / creator of the value';
COMMENT ON COLUMN user_value_time.last_update IS 'for fast recalculation';
COMMENT ON COLUMN user_value_time.description IS 'temp field used during dev phase for easy value to trm assigns';
COMMENT ON COLUMN user_value_time.excluded IS 'the default exclude setting for most users';

--
-- table structure for time values where the time is expected to be never user in a search related up to four prime phrase
--

CREATE TABLE IF NOT EXISTS value_time_prime
(
    group_id        BIGSERIAL PRIMARY KEY,
    time_value      timestamp NOT NULL,
    user_id         bigint                    DEFAULT NULL,
    source_id       bigint                    DEFAULT NULL,
    description     text,
    excluded        smallint                  DEFAULT NULL,
    last_update     timestamp        NULL     DEFAULT NULL,
    share_type_id   smallint                  DEFAULT NULL,
    protect_id      smallint           NOT NULL DEFAULT '1'
);

COMMENT ON TABLE value_time_prime IS 'for the most often used time values';
COMMENT ON COLUMN value_time_prime.group_id IS 'the prime index to find the values';
COMMENT ON COLUMN value_time_prime.user_id IS 'the owner / creator of the value';
COMMENT ON COLUMN value_time_prime.last_update IS 'for fast recalculation';
COMMENT ON COLUMN value_time_prime.description IS 'temp field used during dev phase for easy value to trm assigns';
COMMENT ON COLUMN value_time_prime.excluded IS 'the default exclude setting for most users';

--
-- table structure to store the user specific changes of time values where the time is expected to be never user in a search related up to four prime phrase
--

CREATE TABLE IF NOT EXISTS user_value_time_prime
(
    group_id        BIGSERIAL PRIMARY KEY,
    user_id         bigint                    DEFAULT NULL,
    time_value      timestamp NOT NULL,
    source_id       bigint                    DEFAULT NULL,
    description     text,
    excluded        smallint                  DEFAULT NULL,
    last_update     timestamp        NULL     DEFAULT NULL,
    share_type_id   smallint                  DEFAULT NULL,
    protect_id      smallint           NOT NULL DEFAULT '1'
);

COMMENT ON TABLE user_value_time_prime IS 'to store the user specific changes of the most often used time values';
COMMENT ON COLUMN user_value_time_prime.group_id IS 'the prime index to find the values';
COMMENT ON COLUMN user_value_time_prime.user_id IS 'the owner / creator of the value';
COMMENT ON COLUMN user_value_time_prime.last_update IS 'for fast recalculation';
COMMENT ON COLUMN user_value_time_prime.description IS 'temp field used during dev phase for easy value to trm assigns';
COMMENT ON COLUMN user_value_time_prime.excluded IS 'the default exclude setting for most users';

--
-- table structure for time values where the time is expected to be never user in a search related to more than 16 phrases
--

CREATE TABLE IF NOT EXISTS value_time_big
(
    group_id        TEXT NOT NULL,
    time_value      timestamp NOT NULL,
    user_id         bigint                    DEFAULT NULL,
    source_id       bigint                    DEFAULT NULL,
    description     text,
    excluded        smallint                  DEFAULT NULL,
    last_update     timestamp        NULL     DEFAULT NULL,
    share_type_id   smallint                  DEFAULT NULL,
    protect_id      smallint           NOT NULL DEFAULT '1'
);

COMMENT ON TABLE value_time_big IS 'for the most often used time values';
COMMENT ON COLUMN value_time_big.group_id IS 'the prime index to find the values';
COMMENT ON COLUMN value_time_big.user_id IS 'the owner / creator of the value';
COMMENT ON COLUMN value_time_big.last_update IS 'for fast recalculation';
COMMENT ON COLUMN value_time_big.description IS 'temp field used during dev phase for easy value to trm assigns';
COMMENT ON COLUMN value_time_big.excluded IS 'the default exclude setting for most users';

--
-- table structure to store the user specific changes of time values where the time is expected to be never user in a search related to more than 16 phrases
--

CREATE TABLE IF NOT EXISTS user_value_time_big
(
    group_id        TEXT NOT NULL,
    user_id         bigint                    DEFAULT NULL,
    time_value      timestamp NOT NULL,
    source_id       bigint                    DEFAULT NULL,
    description     text,
    excluded        smallint                  DEFAULT NULL,
    last_update     timestamp        NULL     DEFAULT NULL,
    share_type_id   smallint                  DEFAULT NULL,
    protect_id      smallint           NOT NULL DEFAULT '1'
);

COMMENT ON TABLE user_value_time_big IS 'to store the user specific changes of the most often used time values';
COMMENT ON COLUMN user_value_time_big.group_id IS 'the prime index to find the values';
COMMENT ON COLUMN user_value_time_big.user_id IS 'the owner / creator of the value';
COMMENT ON COLUMN user_value_time_big.last_update IS 'for fast recalculation';
COMMENT ON COLUMN user_value_time_big.description IS 'temp field used during dev phase for easy value to trm assigns';
COMMENT ON COLUMN user_value_time_big.excluded IS 'the default exclude setting for most users';

-- --------------------------------------------------------

--
-- table structure for geo location values
--

CREATE TABLE IF NOT EXISTS value_geo
(
    group_id        char(112) PRIMARY KEY,
    geo_value       point NOT NULL,
    user_id         bigint                    DEFAULT NULL,
    source_id       bigint                    DEFAULT NULL,
    description     text,
    excluded        smallint                  DEFAULT NULL,
    last_update     timestamp        NULL     DEFAULT NULL,
    share_type_id   smallint                  DEFAULT NULL,
    protect_id      smallint           NOT NULL DEFAULT '1'
);

COMMENT ON TABLE value_geo IS 'for the most often used geo location values';
COMMENT ON COLUMN value_geo.group_id IS 'the prime index to find the values';
COMMENT ON COLUMN value_geo.user_id IS 'the owner / creator of the value';
COMMENT ON COLUMN value_geo.last_update IS 'for fast recalculation';
COMMENT ON COLUMN value_geo.description IS 'temp field used during dev phase for easy value to trm assigns';
COMMENT ON COLUMN value_geo.excluded IS 'the default exclude setting for most users';

--
-- table structure to store the user specific changes of geo locations
--

CREATE TABLE IF NOT EXISTS user_value_geo
(
    group_id        char(112) NOT NULL,
    user_id         bigint                    DEFAULT NULL,
    geo_value       point NOT NULL,
    source_id       bigint                    DEFAULT NULL,
    description     text,
    excluded        smallint                  DEFAULT NULL,
    last_update     timestamp        NULL     DEFAULT NULL,
    share_type_id   smallint                  DEFAULT NULL,
    protect_id      smallint           NOT NULL DEFAULT '1'
);

COMMENT ON TABLE user_value_geo IS 'to store the user specific changes of the most often used geo location values';
COMMENT ON COLUMN user_value_geo.group_id IS 'the prime index to find the values';
COMMENT ON COLUMN user_value_geo.user_id IS 'the owner / creator of the value';
COMMENT ON COLUMN user_value_geo.last_update IS 'for fast recalculation';
COMMENT ON COLUMN user_value_geo.description IS 'temp field used during dev phase for easy value to trm assigns';
COMMENT ON COLUMN user_value_geo.excluded IS 'the default exclude setting for most users';

--
-- table structure for public geo location values related up to four prime phrase that have never changed the owner, does not have a description and are rarely updated
--

CREATE TABLE IF NOT EXISTS value_geo_standard_prime
(
    group_id  BIGSERIAL PRIMARY KEY,
    geo_value point NOT NULL,
    source_id  int DEFAULT NULL
);

COMMENT ON TABLE value_geo_standard_prime IS 'for public unprotected geo locations related up to four prime phrase that have never changed the owner, does not have a description and are rarely updated';
COMMENT ON COLUMN value_geo_standard_prime.group_id IS 'the prime index to find the geo location';

--
-- table structure for public geo location values that have never changed the owner, does not have a description and are rarely updated
--

CREATE TABLE IF NOT EXISTS value_geo_standard
(
    group_id  char(112) PRIMARY KEY,
    geo_value point NOT NULL,
    source_id  bigint DEFAULT NULL
);

COMMENT ON TABLE value_geo_standard IS 'for public unprotected geo locations that have never changed the owner, does not have a description and are rarely updated';
COMMENT ON COLUMN value_geo_standard.group_id IS 'the prime index to find the geo location';

--
-- table structure for geo location values related up to four prime phrase
--

CREATE TABLE IF NOT EXISTS value_geo_prime
(
    group_id        BIGSERIAL NOT NULL,
    geo_value       point NOT NULL,
    user_id         bigint                    DEFAULT NULL,
    source_id       bigint                    DEFAULT NULL,
    description     text,
    excluded        smallint                  DEFAULT NULL,
    last_update     timestamp        NULL     DEFAULT NULL,
    share_type_id   smallint                  DEFAULT NULL,
    protect_id      smallint           NOT NULL DEFAULT '1'
);

COMMENT ON TABLE value_geo_prime IS 'for the most often used geo location values';
COMMENT ON COLUMN value_geo_prime.group_id IS 'the prime index to find the geo locations';
COMMENT ON COLUMN value_geo_prime.user_id IS 'the owner / creator of the value';
COMMENT ON COLUMN value_geo_prime.last_update IS 'for fast recalculation';
COMMENT ON COLUMN value_geo_prime.description IS 'temp field used during dev phase for easy value to trm assigns';
COMMENT ON COLUMN value_geo_prime.excluded IS 'the default exclude setting for most users';

--
-- table structure to store the user specific changes of geo locations related up to four prime phrase
--

CREATE TABLE IF NOT EXISTS user_value_geo_prime
(
    group_id        BIGSERIAL NOT NULL,
    user_id         bigint                    DEFAULT NULL,
    geo_value       point NOT NULL,
    source_id       bigint                    DEFAULT NULL,
    description     text,
    excluded        smallint                  DEFAULT NULL,
    last_update     timestamp        NULL     DEFAULT NULL,
    share_type_id   smallint                  DEFAULT NULL,
    protect_id      smallint           NOT NULL DEFAULT '1'
);

COMMENT ON TABLE user_value_geo_prime IS 'to store the user specific changes of the most often used geo location values';
COMMENT ON COLUMN user_value_geo_prime.group_id IS 'the prime index to find the geo locations';
COMMENT ON COLUMN user_value_geo_prime.user_id IS 'the owner / creator of the value';
COMMENT ON COLUMN user_value_geo_prime.last_update IS 'for fast recalculation';
COMMENT ON COLUMN user_value_geo_prime.description IS 'temp field used during dev phase for easy value to trm assigns';
COMMENT ON COLUMN user_value_geo_prime.excluded IS 'the default exclude setting for most users';

--
-- table structure for geo location values related to more than 16 phrases
--

CREATE TABLE IF NOT EXISTS value_geo_big
(
    group_id        BIGSERIAL NOT NULL,
    geo_value       point NOT NULL,
    user_id         bigint                    DEFAULT NULL,
    source_id       bigint                    DEFAULT NULL,
    description     text,
    excluded        smallint                  DEFAULT NULL,
    last_update     timestamp        NULL     DEFAULT NULL,
    share_type_id   smallint                  DEFAULT NULL,
    protect_id      smallint           NOT NULL DEFAULT '1'
);

COMMENT ON TABLE value_geo_big IS 'for the most often used geo location values';
COMMENT ON COLUMN value_geo_big.group_id IS 'the prime index to find the geo locations';
COMMENT ON COLUMN value_geo_big.user_id IS 'the owner / creator of the value';
COMMENT ON COLUMN value_geo_big.last_update IS 'for fast recalculation';
COMMENT ON COLUMN value_geo_big.description IS 'temp field used during dev phase for easy value to trm assigns';
COMMENT ON COLUMN value_geo_big.excluded IS 'the default exclude setting for most users';

--
-- table structure to store the user specific changes of geo locations related to more than 16 phrases
--

CREATE TABLE IF NOT EXISTS user_value_geo_big
(
    group_id        BIGSERIAL NOT NULL,
    user_id         bigint                    DEFAULT NULL,
    geo_value       point NOT NULL,
    source_id       bigint                    DEFAULT NULL,
    description     text,
    excluded        smallint                  DEFAULT NULL,
    last_update     timestamp        NULL     DEFAULT NULL,
    share_type_id   smallint                  DEFAULT NULL,
    protect_id      smallint           NOT NULL DEFAULT '1'
);

COMMENT ON TABLE user_value_geo_big IS 'to store the user specific changes of the most often used geo location values';
COMMENT ON COLUMN user_value_geo_big.group_id IS 'the prime index to find the geo locations';
COMMENT ON COLUMN user_value_geo_big.user_id IS 'the owner / creator of the value';
COMMENT ON COLUMN user_value_geo_big.last_update IS 'for fast recalculation';
COMMENT ON COLUMN user_value_geo_big.description IS 'temp field used during dev phase for easy value to trm assigns';
COMMENT ON COLUMN user_value_geo_big.excluded IS 'the default exclude setting for most users';

-- --------------------------------------------------------

--
-- table structure for the header of a list of numbers that differ only by the timestamp
--

CREATE TABLE IF NOT EXISTS value_time_series
(
    value_time_series_id BIGSERIAL      PRIMARY KEY,
    user_id              bigint         NOT     NULL,
    source_id            bigint         DEFAULT NULL,
    group_id             char(112)      NOT NULL,
    excluded             smallint       DEFAULT NULL,
    share_type_id        smallint         DEFAULT NULL,
    protect_id           smallint         NOT     NULL,
    last_update          timestamp NULL DEFAULT NULL
);

COMMENT ON TABLE user_value_time_series IS 'common parameters for a list of numbers that differ only by the timestamp';
COMMENT ON COLUMN user_value_time_series.user_id IS 'the owner / creator of the number list';

-- --------------------------------------------------------

--
-- table structure for specific user changes in a of numbers that differ only by the timestamp
--

CREATE TABLE IF NOT EXISTS user_value_time_series
(
    value_time_series_id BIGSERIAL          NOT NULL,
    user_id              bigint             NOT NULL,
    source_id            bigint         DEFAULT NULL,
    excluded             smallint       DEFAULT NULL,
    share_type_id        smallint         DEFAULT NULL,
    protect_id           smallint             NOT NULL,
    last_update          timestamp NULL DEFAULT NULL
);

COMMENT ON TABLE user_value_time_series IS 'common parameters for a list of numbers that differ only by the timestamp';

--
-- table structure for table value_time_series_prime
--

CREATE TABLE IF NOT EXISTS value_time_series_prime
(
    value_time_series_id BIGSERIAL      PRIMARY KEY,
    user_id              bigint         NOT     NULL,
    source_id            bigint         DEFAULT NULL,
    group_id             bigint         NOT NULL,
    excluded             smallint       DEFAULT NULL,
    share_type_id        smallint         DEFAULT NULL,
    protect_id           smallint         NOT     NULL,
    last_update          timestamp NULL DEFAULT NULL
);

COMMENT ON TABLE value_time_series IS 'common parameters for a list of intra-day values';

-- --------------------------------------------------------

--
-- table structure for table value_time_series
--

CREATE TABLE IF NOT EXISTS user_value_time_series_prime
(
    value_time_series_id BIGSERIAL          NOT NULL,
    user_id              bigint             NOT NULL,
    source_id            bigint         DEFAULT NULL,
    excluded             smallint       DEFAULT NULL,
    share_type_id        smallint         DEFAULT NULL,
    protect_id           smallint             NOT NULL,
    last_update          timestamp NULL DEFAULT NULL
);

COMMENT ON TABLE user_value_time_series IS 'common parameters for a user specific list of intra-day values';

-- --------------------------------------------------------

--
-- table structure for table value_ts_data
--

CREATE TABLE IF NOT EXISTS value_ts_data
(
    value_time_series_id BIGSERIAL PRIMARY KEY,
    val_time             timestamp NOT NULL,
    number               float     NOT NULL
);

COMMENT ON TABLE value_ts_data IS 'for efficient saving of daily or intra-day values';

-- --------------------------------------------------------

--
-- table structure for table formula_element_types
--

CREATE TABLE IF NOT EXISTS formula_element_types
(
    formula_element_type_id BIGSERIAL     PRIMARY KEY,
    type_name               varchar(200)  NOT NULL,
    code_id                 varchar(100)  DEFAULT NULL,
    description             text
);

--
-- table structure for table formula_elements
--

CREATE TABLE IF NOT EXISTS formula_elements
(
    formula_element_id      BIGSERIAL PRIMARY KEY,
    formula_id              bigint NOT NULL,
    user_id                 bigint NOT NULL,
    order_nbr               bigint NOT NULL,
    formula_element_type_id bigint NOT NULL,
    ref_id                  bigint       DEFAULT NULL,
    resolved_text           varchar(200) DEFAULT NULL
);

COMMENT ON TABLE formula_elements IS 'cache for fast update of formula resolved text';
COMMENT ON COLUMN formula_elements.ref_id IS 'either a term, verb or formula id';

--
-- table structure for table formula_types
--

CREATE TABLE IF NOT EXISTS formula_types
(
    formula_type_id BIGSERIAL PRIMARY KEY,
    name            varchar(100) NOT NULL,
    description     text         NOT NULL,
    code_id         varchar(255) NOT NULL
);

--
-- table structure for table formulas
--

CREATE TABLE IF NOT EXISTS formulas
(
    formula_id        BIGSERIAL PRIMARY KEY,
    formula_name      varchar(100) NOT NULL,
    user_id           bigint                DEFAULT NULL,
    formula_text      text         NOT NULL,
    resolved_text     text         NOT NULL,
    description       text,
    formula_type_id   bigint                DEFAULT NULL,
    all_values_needed smallint              DEFAULT NULL,
    last_update       timestamp    NULL     DEFAULT NULL,
    usage             bigint       NULL     DEFAULT NULL,
    excluded          smallint              DEFAULT NULL,
    share_type_id     smallint                DEFAULT NULL,
    protect_id        smallint       NOT NULL DEFAULT '1'
);

COMMENT ON COLUMN formulas.formula_name IS 'short name of the formula';
COMMENT ON COLUMN formulas.formula_text IS 'the coded formula; e.g. \\f1 for formula with ID1';
COMMENT ON COLUMN formulas.resolved_text IS 'the formula in user readable format';
COMMENT ON COLUMN formulas.description IS 'additional to comments because many formulas have this';
COMMENT ON COLUMN formulas.all_values_needed IS 'calculate the result only if all values used in the formula are not null';
COMMENT ON COLUMN formulas.last_update IS 'time of the last calculation relevant update';

--
-- table structure for table user_formulas
--

CREATE TABLE IF NOT EXISTS user_formulas
(
    formula_id        BIGSERIAL PRIMARY KEY,
    user_id           bigint    NOT NULL,
    formula_name      varchar(200)   DEFAULT NULL,
    formula_text      text,
    resolved_text     text,
    description       text,
    formula_type_id   bigint         DEFAULT NULL,
    all_values_needed smallint       DEFAULT NULL,
    share_type_id     smallint         DEFAULT NULL,
    protect_id        smallint    DEFAULT NULL,
    last_update       timestamp NULL DEFAULT NULL,
    usage             bigint    NULL DEFAULT NULL,
    excluded          smallint       DEFAULT NULL
);

--
-- table structure for table formula_link_types
--

CREATE TABLE IF NOT EXISTS formula_link_types
(
    formula_link_type_id BIGSERIAL PRIMARY KEY,
    type_name            varchar(200) NOT NULL,
    code_id              varchar(100)          DEFAULT NULL,
    formula_id           bigint       NOT NULL DEFAULT 1,
    phrase_type_id         bigint       NOT NULL,
    link_type_id         bigint       NOT NULL,
    description          text
);

--
-- table structure for table formula_links
--

CREATE TABLE IF NOT EXISTS formula_links
(
    formula_link_id BIGSERIAL PRIMARY KEY,
    user_id         bigint   DEFAULT NULL,
    formula_id      bigint NOT NULL,
    phrase_id       bigint NOT NULL,
    link_type_id    bigint   DEFAULT NULL,
    order_nbr       bigint DEFAULT NULL,
    excluded        smallint DEFAULT NULL
);

COMMENT ON TABLE formula_links IS 'if the term pattern of a value matches this term pattern';

--
-- table structure for table user_formula_links
--

CREATE TABLE IF NOT EXISTS user_formula_links
(
    formula_link_id BIGSERIAL NOT NULL,
    user_id         bigint    NOT NULL,
    link_type_id    bigint    DEFAULT NULL,
    excluded        smallint  DEFAULT NULL
);

COMMENT ON TABLE user_formula_links IS 'if the term pattern of a value matches this term pattern ';

-- --------------------------------------------------------

--
-- table structure for standard results
--

CREATE TABLE IF NOT EXISTS result_standard_prime
(
    group_id BIGSERIAL PRIMARY KEY,
    result   double precision
);

COMMENT ON TABLE result_standard IS 'table to cache the pure formula results related up to four prime phrase without any related information';
COMMENT ON COLUMN result_standard_prime.group_id IS 'the prime index to find the results';

--
-- table structure for standard results
--

CREATE TABLE IF NOT EXISTS result_standard
(
    group_id char(112) PRIMARY KEY,
    result   double precision
);

COMMENT ON TABLE result_standard IS 'table to cache the pure formula results without any related information';
COMMENT ON COLUMN result_standard.group_id IS 'the prime index to find the results';

--
-- table structure for results with more information to trace the calculation
--

CREATE TABLE IF NOT EXISTS results
(
    group_id        char(112)        PRIMARY KEY,
    numeric_value   double precision NOT NULL,
    last_update     timestamp        DEFAULT NULL,
    formula_id      bigint           NOT NULL,
    source_group_id char(112)        DEFAULT NULL,
    user_id         bigint           DEFAULT NULL,
    excluded        smallint         DEFAULT NULL,
    share_type_id   smallint         DEFAULT NULL,
    protect_id      smallint         DEFAULT NULL
);

COMMENT ON TABLE results                  IS 'table to cache the formula numeric results related to up to 16 phrases';
COMMENT ON COLUMN results.group_id        IS 'the 512-bit prime index to find the numeric result';
COMMENT ON COLUMN results.numeric_value   IS 'the numeric value given by the user';
COMMENT ON COLUMN results.last_update     IS 'timestamp of the last update used also to trigger updates of depending values for fast recalculation for fast recalculation';
COMMENT ON COLUMN results.formula_id      IS 'the id of the formula which has been used to calculate this result';
COMMENT ON COLUMN results.source_group_id IS 'the sorted phrase list used to calculate this result';
COMMENT ON COLUMN results.user_id         IS 'the id of the user who has requested the calculation';
COMMENT ON COLUMN results.excluded        IS 'true if a user, but not all, have removed it';
COMMENT ON COLUMN results.share_type_id   IS 'to restrict the access';
COMMENT ON COLUMN results.protect_id      IS 'to protect against unwanted changes';

--
-- table structure for the most often requested results related up to four prime phrase
--

CREATE TABLE IF NOT EXISTS result_prime
(
    group_id        BIGSERIAL PRIMARY KEY,
    result          double precision,
    formula_id      bigint         NOT NULL,
    source_group_id bigint         DEFAULT NULL,
    user_id         bigint         DEFAULT NULL,
    last_update     timestamp NULL DEFAULT NULL
);

COMMENT ON TABLE result_prime IS 'table to cache the formula results related up to four prime phrases';
COMMENT ON COLUMN result_prime.group_id IS 'the prime index to find the results';
COMMENT ON COLUMN result_prime.formula_id IS 'the id of the formula which has been used to calculate the result number';
COMMENT ON COLUMN result_prime.source_group_id IS 'the sorted phrase list used to calculate the result number';
COMMENT ON COLUMN result_prime.user_id IS 'the id of the user who has requested the calculation';
COMMENT ON COLUMN result_prime.last_update IS 'time of last value update mainly used for recovery in case of inconsistencies, empty in case this value is dirty and needs to be updated';

--
-- table structure for results related more than 16 phrases
--

CREATE TABLE IF NOT EXISTS result_big
(
    group_id        TEXT PRIMARY KEY,
    result          double precision,
    formula_id      bigint         NOT NULL,
    source_group_id TEXT           DEFAULT NULL,
    user_id         bigint         DEFAULT NULL,
    last_update     timestamp NULL DEFAULT NULL
);

COMMENT ON TABLE result_big IS 'table to cache the formula results related up to four prime phrases';
COMMENT ON COLUMN result_big.group_id IS 'the prime index to find the results';
COMMENT ON COLUMN result_big.formula_id IS 'the id of the formula which has been used to calculate the result number';
COMMENT ON COLUMN result_big.source_group_id IS 'the sorted phrase list used to calculate the result number';
COMMENT ON COLUMN result_big.user_id IS 'the id of the user who has requested the calculation';
COMMENT ON COLUMN result_big.last_update IS 'time of last value update mainly used for recovery in case of inconsistencies, empty in case this value is dirty and needs to be updated';

-- --------------------------------------------------------

--
-- table structure for table view_type_list
--

CREATE TABLE IF NOT EXISTS view_types
(
    view_type_id BIGSERIAL PRIMARY KEY,
    type_name    varchar(200) NOT NULL,
    description  text         NOT NULL,
    code_id      varchar(100) DEFAULT NULL
);

COMMENT ON TABLE view_types IS 'to group the masks a link a basic format';

--
-- table structure for table views
--

CREATE TABLE IF NOT EXISTS views
(
    view_id       BIGSERIAL PRIMARY KEY,
    user_id       bigint                DEFAULT NULL,
    view_name     varchar(100) NOT NULL,
    description   text                  DEFAULT NULL,
    view_type_id  bigint                DEFAULT NULL,
    code_id       varchar(100)          DEFAULT NULL,
    excluded      smallint              DEFAULT NULL,
    share_type_id smallint              DEFAULT NULL,
    protect_id    smallint     NOT NULL DEFAULT '1'
);

COMMENT ON TABLE views IS 'all user interfaces should be listed here';
COMMENT ON COLUMN views.view_name IS 'for easy selection';

--
-- table structure for table user_views
--

CREATE TABLE IF NOT EXISTS user_views
(
    view_id       bigint   NOT NULL,
    user_id       bigint   NOT NULL,
    view_name     varchar(200)      DEFAULT NULL,
    description   text,
    view_type_id  bigint            DEFAULT NULL,
    excluded      smallint          DEFAULT NULL,
    share_type_id smallint          DEFAULT NULL,
    protect_id    smallint NOT NULL DEFAULT '1'
);

COMMENT ON TABLE user_views IS 'user specific mask settings';

-- --------------------------------------------------------

--
-- table structure for table component_link_types
--

CREATE TABLE IF NOT EXISTS component_link_types
(
    component_link_type_id      BIGSERIAL PRIMARY KEY,
    type_name                   varchar(200) NOT NULL,
    code_id                     varchar(50)  NOT NULL
);

--
-- table structure for table component_position_types
--

CREATE TABLE IF NOT EXISTS component_position_types
(
    component_position_type_id      BIGSERIAL PRIMARY KEY,
    type_name                       varchar(100) NOT NULL,
    description                     text         NOT NULL,
    code_id                         varchar(50)  NOT NULL
);

COMMENT ON TABLE component_position_types IS 'sideways or down';

--
-- table structure for table component_types
--

CREATE TABLE IF NOT EXISTS component_types
(
    component_type_id BIGSERIAL PRIMARY KEY,
    type_name              varchar(100) NOT NULL,
    description            text DEFAULT NULL,
    code_id                varchar(100) NOT NULL
);

COMMENT ON TABLE component_types IS 'fixed text, term or formula result';

--
-- table structure for table components
--

CREATE TABLE IF NOT EXISTS components
(
    component_id                BIGSERIAL PRIMARY KEY,
    user_id                     bigint       NOT NULL,
    component_name              varchar(100) NOT NULL,
    description                 text,
    component_type_id           bigint                DEFAULT NULL,
    code_id                     varchar(100)          DEFAULT NULL,
    ui_msg_code_id              varchar(100)          DEFAULT NULL,
    word_id_row                 bigint                DEFAULT NULL,
    formula_id                  bigint                DEFAULT NULL,
    word_id_col                 bigint                DEFAULT NULL,
    word_id_col2                bigint                DEFAULT NULL,
    excluded                    smallint              DEFAULT NULL,
    share_type_id               smallint              DEFAULT NULL,
    protect_id                  smallint     NOT NULL DEFAULT '1',
    linked_component_id         bigint                DEFAULT NULL,
    component_link_type_id      bigint                DEFAULT NULL,
    link_type_id                bigint                DEFAULT NULL
);

COMMENT ON TABLE components IS 'the single components of a view';
COMMENT ON COLUMN components.component_name IS 'the unique name used to select a component by the user';
COMMENT ON COLUMN components.component_type_id IS 'to select the predefined functionality';
COMMENT ON COLUMN components.code_id IS 'used for system components to select the component by the program code';
COMMENT ON COLUMN components.ui_msg_code_id IS 'used for system components the id to select the language specific user interface message e.g. "add word"';
COMMENT ON COLUMN components.word_id_row IS 'for a tree the related value the start node';
COMMENT ON COLUMN components.formula_id IS 'used for type 6';
COMMENT ON COLUMN components.word_id_col IS 'to define the type for the table columns';
COMMENT ON COLUMN components.word_id_col2 IS 'e.g. "quarter" to show the quarters between the year columns or the second axis of a chart';
COMMENT ON COLUMN components.linked_component_id IS 'to link this component to another component';
COMMENT ON COLUMN components.component_link_type_id IS 'to define how this entry links to the other entry';
COMMENT ON COLUMN components.link_type_id IS 'e.g. for type 4 to select possible terms';

--
-- table structure for table user_components
--

CREATE TABLE IF NOT EXISTS user_components
(
    component_id      bigint   NOT NULL,
    user_id                bigint   NOT NULL,
    component_name    varchar(200)      DEFAULT NULL,
    description            text,
    component_type_id bigint            DEFAULT NULL,
    word_id_row            bigint            DEFAULT NULL,
    word_id_col            bigint            DEFAULT NULL,
    word_id_col2           bigint            DEFAULT NULL,
    formula_id             bigint            DEFAULT NULL,
    excluded               bigint            DEFAULT NULL,
    share_type_id          smallint          DEFAULT NULL,
    protect_id             smallint NOT NULL DEFAULT '1',
    link_type_id           bigint            DEFAULT NULL
);

-- --------------------------------------------------------

--
-- table structure for table component_links
--

CREATE TABLE IF NOT EXISTS component_links
(
    component_link_id      BIGSERIAL PRIMARY KEY,
    user_id                bigint   NOT NULL,
    view_id                bigint   NOT NULL,
    component_id           bigint   NOT NULL,
    order_nbr              bigint   NOT NULL,
    position_type          bigint   NOT NULL DEFAULT '2',
    excluded               smallint          DEFAULT NULL,
    share_type_id          smallint          DEFAULT NULL,
    protect_id             smallint NOT NULL DEFAULT '1'
);

COMMENT ON TABLE component_links IS 'A named mask entry can be used in several masks e.g. the company name';
COMMENT ON COLUMN component_links.position_type IS '1=side, 2 =below';

--
-- table structure for table user_component_links
--

CREATE TABLE IF NOT EXISTS user_component_links
(
    component_link_id bigint   NOT NULL,
    user_id           bigint   NOT NULL,
    order_nbr         bigint            DEFAULT NULL,
    position_type     bigint            DEFAULT NULL,
    excluded          smallint          DEFAULT NULL,
    share_type_id     smallint          DEFAULT NULL,
    protect_id        smallint NOT NULL DEFAULT '1'
);

-- --------------------------------------------------------

--
-- table structure for table view_link_types
--

CREATE TABLE IF NOT EXISTS view_link_types
(
    view_link_type_id BIGSERIAL PRIMARY KEY,
    type_name         varchar(200) NOT NULL,
    comment           text         NOT NULL
);

--
-- table structure for table view_term_links
--

CREATE TABLE IF NOT EXISTS view_term_links
(
    view_term_link_id BIGSERIAL PRIMARY KEY,
    term_id           bigint NOT NULL,
    type_id           bigint NOT NULL   DEFAULT '1',
    link_type_id      bigint            DEFAULT NULL,
    view_id           bigint            DEFAULT NULL,
    user_id           bigint NOT NULL,
    description       text   NOT NULL,
    excluded          smallint          DEFAULT NULL,
    share_type_id     smallint          DEFAULT NULL,
    protect_id        smallint NOT NULL DEFAULT NULL
);

COMMENT ON TABLE view_term_links IS 'used to define the default mask for a term or a term group';
COMMENT ON COLUMN view_term_links.type_id IS '1 = from_term_id is link the terms table; 2=link to the term_links table;3=to term_groups';

--
-- table structure for table user_view_term_links
--

CREATE TABLE IF NOT EXISTS user_view_term_links
(
    view_term_link_id BIGSERIAL PRIMARY KEY,
    type_id           bigint NOT NULL   DEFAULT '1',
    link_type_id      bigint            DEFAULT NULL,
    user_id           bigint NOT NULL,
    description       text   NOT NULL,
    excluded          smallint          DEFAULT NULL,
    share_type_id     smallint          DEFAULT NULL,
    protect_id        smallint NOT NULL DEFAULT NULL
);

-- --------------------------------------------------------

--
-- table structure for future use
--

-- --------------------------------------------------------

--
-- table structure for table value_formula_links
--

CREATE TABLE IF NOT EXISTS value_formula_links
(
    value_formula_link_id BIGSERIAL PRIMARY KEY,
    group_id              bigint DEFAULT NULL,
    formula_id            bigint DEFAULT NULL,
    user_id               bigint DEFAULT NULL,
    condition_formula_id  bigint DEFAULT NULL,
    comment               text
);

COMMENT ON TABLE value_formula_links IS 'used to select if a saved value should be used or a calculated value';
COMMENT ON COLUMN value_formula_links.condition_formula_id IS 'if true or 1  to formula is preferred';

-- --------------------------------------------------------

--
-- Structure for view phrase_prime (phrases with an id less than 2^16 so that 4 phrase id fit in a 64 bit db key)
--

CREATE OR REPLACE VIEW prime_phrases AS
SELECT w.word_id   AS phrase_id,
       w.user_id,
       w.word_name AS phrase_name,
       w.description,
       w.values,
       w.phrase_type_id,
       w.excluded,
       w.share_type_id,
       w.protect_id
FROM words AS w
WHERE w.word_id < 32767 -- 2^16 / 2 - 1
UNION
SELECT (t.triple_id * -(1)) AS phrase_id,
       t.user_id,
       CASE WHEN (t.triple_name IS NULL) THEN
            CASE WHEN (t.name_given IS NULL)
                 THEN t.name_generated
                 ELSE t.name_given END
            ELSE t.triple_name END AS phrase_name,
       t.description,
       t.values,
       t.phrase_type_id,
       t.excluded,
       t.share_type_id,
       t.protect_id
FROM triples AS t
WHERE t.triple_id < 32767; -- 2^16 / 2 - 1

--
-- Structure for view phrases
--

CREATE OR REPLACE VIEW phrases AS
SELECT w.word_id   AS phrase_id,
       w.user_id,
       w.word_name AS phrase_name,
       w.description,
       w.values,
       w.phrase_type_id,
       w.excluded,
       w.share_type_id,
       w.protect_id
FROM words AS w
UNION
SELECT (t.triple_id * -(1)) AS phrase_id,
       t.user_id,
       CASE WHEN (t.triple_name IS NULL) THEN
            CASE WHEN (t.name_given IS NULL)
                 THEN t.name_generated
                 ELSE t.name_given END
            ELSE t.triple_name END AS phrase_name,
       t.description,
       t.values,
       t.phrase_type_id,
       t.excluded,
       t.share_type_id,
       t.protect_id
FROM triples AS t;

--
-- Structure for the user_phrases view
--

CREATE OR REPLACE VIEW user_prime_phrases AS
SELECT w.word_id   AS phrase_id,
       w.user_id,
       w.word_name AS phrase_name,
       w.description,
       w.values,
       w.excluded,
       w.share_type_id,
       w.protect_id
FROM user_words AS w
WHERE w.word_id < 32767 -- 2^16 / 2 - 1
UNION
SELECT (t.triple_id * -(1)) AS phrase_id,
       t.user_id,
       CASE WHEN (t.triple_name IS NULL) THEN
            CASE WHEN (t.name_given IS NULL)
                 THEN t.name_generated
                 ELSE t.name_given END
            ELSE t.triple_name END AS phrase_name,
       t.description,
       t.values,
       t.excluded,
       t.share_type_id,
       t.protect_id
FROM user_triples AS t
WHERE t.triple_id < 32767; -- 2^16 / 2 - 1

--
-- Structure for the user_phrases view
--

CREATE OR REPLACE VIEW user_phrases AS
SELECT w.word_id   AS phrase_id,
       w.user_id,
       w.word_name AS phrase_name,
       w.description,
       w.values,
       w.excluded,
       w.share_type_id,
       w.protect_id
  FROM user_words AS w
UNION
SELECT (t.triple_id * -(1)) AS phrase_id,
       t.user_id,
       CASE WHEN (t.triple_name IS NULL) THEN
            CASE WHEN (t.name_given IS NULL)
                 THEN t.name_generated
                 ELSE t.name_given END
            ELSE t.triple_name END AS phrase_name,
       t.description,
       t.values,
       t.excluded,
       t.share_type_id,
       t.protect_id
  FROM user_triples AS t;

--
-- Structure for view terms
--

CREATE OR REPLACE VIEW terms AS
SELECT ((w.word_id * 2) - 1) AS term_id,
       w.user_id,
       w.word_name           AS term_name,
       w.description,
       w.values              AS usage,
       w.phrase_type_id        AS term_type_id,
       w.excluded,
       w.share_type_id,
       w.protect_id,
       ''                    AS formula_text,
       ''                    AS resolved_text
FROM words AS w
WHERE w.phrase_type_id <> 10 OR w.phrase_type_id IS NULL
UNION
SELECT ((t.triple_id * -2) + 1)                                                  AS term_id,
       t.user_id,
       CASE WHEN (t.triple_name IS NULL) THEN
                CASE WHEN (t.name_given IS NULL)
                     THEN t.name_generated
                     ELSE t.name_given END
            ELSE t.triple_name END AS phrase_name,
       t.description,
       t.values                                                                     AS usage,
       t.phrase_type_id,
       t.excluded,
       t.share_type_id,
       t.protect_id,
       ''                    AS formula_text,
       ''                    AS resolved_text
FROM triples AS t
UNION
SELECT (f.formula_id * 2) AS term_id,
       f.user_id,
       f.formula_name     AS term_name,
       f.description,
       f.usage            AS usage,
       f.formula_type_id  AS term_type_id,
       f.excluded,
       f.share_type_id,
       f.protect_id,
       f.formula_text     AS formula_text,
       f.resolved_text    AS resolved_text
FROM formulas AS f
UNION
SELECT (v.verb_id * -2) AS term_id,
       NULL            AS user_id,
       v.verb_name     AS term_name,
       v.description,
       v.words         AS usage,
       NULL            AS term_type_id,
       NULL            AS excluded,
       1               AS share_type_id,
       3               AS protect_id,
       ''              AS formula_text,
       ''              AS resolved_text
FROM verbs AS v
;

--
-- Structure for view user_terms
--

CREATE OR REPLACE VIEW user_terms AS
SELECT ((w.word_id * 2) - 1) AS term_id,
       w.user_id,
       w.word_name           AS term_name,
       w.description,
       w.values              AS usage,
       w.excluded,
       w.share_type_id,
       w.protect_id,
       ''                    AS formula_text,
       ''                    AS resolved_text
FROM user_words AS w
WHERE w.phrase_type_id <> 10
UNION
SELECT ((t.triple_id * -2) + 1)  AS term_id,
       t.user_id,
       CASE WHEN (t.triple_name IS NULL) THEN
                CASE WHEN (t.name_given IS NULL)
                     THEN t.name_generated
                     ELSE t.name_given END
            ELSE t.triple_name END AS phrase_name,
       t.description,
       t.values                  AS usage,
       t.excluded,
       t.share_type_id,
       t.protect_id,
       ''                        AS formula_text,
       ''                        AS resolved_text
FROM user_triples AS t
UNION
SELECT (f.formula_id * 2) AS term_id,
       f.user_id,
       f.formula_name     AS term_name,
       f.description,
       f.usage            AS usage,
       f.excluded,
       f.share_type_id,
       f.protect_id,
       f.formula_text     AS formula_text,
       f.resolved_text    AS resolved_text
FROM user_formulas AS f
UNION
SELECT (v.verb_id * -2) AS term_id,
       NULL            AS user_id,
       v.verb_name     AS term_name,
       v.description,
       v.words         AS usage,
       NULL            AS excluded,
       1               AS share_type_id,
       3               AS protect_id,
       ''              AS formula_text,
       ''              AS resolved_text
FROM verbs AS v
;

--
-- Structure for view change_table_fields
--

CREATE OR REPLACE VIEW change_table_fields AS
SELECT f.change_field_id                              AS change_table_field_id,
       concat(t.change_table_id, f.change_field_name) AS change_table_field_name,
       f.description,
       CASE WHEN (f.code_id IS NULL) THEN concat(t.change_table_id, f.change_field_name) ELSE f.code_id END AS code_id
FROM change_fields AS f,
     change_tables AS t
WHERE f.table_id = t.change_table_id;

-- --------------------------------------------------------

--
-- Indexes for tables
-- remark: no index needed for preloaded tables such as phrase types
--

-- --------------------------------------------------------

--
-- Indexes for table config
--
CREATE UNIQUE INDEX config_idx ON config (code_id);

--
-- Indexes for table sys_log
--
CREATE INDEX sys_log_time ON sys_log (sys_log_time);
CREATE INDEX sys_log_type_idx ON sys_log (sys_log_type_id);
CREATE INDEX sys_log_function_idx ON sys_log (sys_log_function_id);
CREATE INDEX sys_log_status_idx ON sys_log (sys_log_status_id);

--
-- Indexes for table sys_script_times
--
CREATE INDEX sys_script_time_idx ON sys_script_times (sys_script_id);

--
-- Indexes for table calc_and_cleanup_tasks
--
CREATE INDEX calc_and_cleanup_tasks_user_idx ON calc_and_cleanup_tasks (user_id);
CREATE INDEX calc_and_cleanup_tasks_request_idx ON calc_and_cleanup_tasks (request_time);
CREATE INDEX calc_and_cleanup_tasks_start_idx ON calc_and_cleanup_tasks (start_time);
CREATE INDEX calc_and_cleanup_tasks_type_idx ON calc_and_cleanup_tasks (calc_and_cleanup_task_type_id);

-- --------------------------------------------------------

--
-- Indexes for table users
--
CREATE UNIQUE INDEX user_name_idx ON users (user_name);
CREATE INDEX user_type_idx ON users (user_type_id);

-- --------------------------------------------------------

--
-- Indexes for table change_fields
--
CREATE INDEX change_field_table_idx ON change_fields (table_id);

--
-- Indexes for table changes
--
CREATE INDEX change_table_idx ON changes (change_field_id, row_id);
CREATE INDEX change_action_idx ON changes (change_action_id);

--
-- Indexes for table change_links
--
CREATE INDEX change_link_user_idx ON change_links (user_id);
CREATE INDEX change_link_table_idx ON change_links (change_table_id);
CREATE INDEX change_link_action_idx ON change_links (change_action_id);

-- --------------------------------------------------------

--
-- Indexes for table words
--
CREATE UNIQUE INDEX word_name_idx ON words (word_name);
CREATE INDEX word_type_idx ON words (phrase_type_id);
CREATE INDEX word_view_idx ON words (view_id);

--
-- Indexes for table user_words
--
ALTER TABLE user_words ADD CONSTRAINT user_words_pkey PRIMARY KEY (word_id, user_id, language_id);
CREATE INDEX user_word_user_idx ON user_words (user_id);
CREATE INDEX user_word_language_idx ON user_words (language_id);
CREATE INDEX user_word_type_idx ON user_words (phrase_type_id);
CREATE INDEX user_word_view_idx ON user_words (view_id);

--
-- Indexes for table triples
--
CREATE UNIQUE INDEX triple_name_idx ON triples (triple_name);
CREATE INDEX triple_type_idx ON triples (phrase_type_id);
CREATE INDEX triple_view_idx ON triples (view_id);

--
-- Indexes for table user_triples
--
CREATE UNIQUE INDEX user_triple_unique_idx ON user_triples (triple_id, user_id, language_id);
CREATE INDEX user_triple_user_idx ON user_triples (user_id);
CREATE INDEX user_triple_idx ON user_triples (triple_id);
CREATE INDEX user_triple_language_idx ON user_words (language_id);
CREATE INDEX user_triple_type_idx ON user_words (phrase_type_id);
CREATE INDEX user_triple_view_idx ON user_words (view_id);

-- --------------------------------------------------------

--
-- Indexes for table groups
--
CREATE UNIQUE INDEX group_name_idx ON groups (group_name);

--
-- Indexes for table user_groups
--
CREATE UNIQUE INDEX user_group_name_idx ON user_groups (group_name, user_id);
CREATE INDEX user_group_idx ON user_groups (group_id);
CREATE INDEX user_group_user_idx ON user_groups (user_id);

--
-- Indexes for table prime groups
--
CREATE UNIQUE INDEX groups_prime_name_idx ON groups_prime (group_name);

--
-- Indexes for table user_groups_prime
--
CREATE UNIQUE INDEX user_groups_prime_name_idx ON user_groups_prime (group_name, user_id);
CREATE INDEX user_groups_prime_idx ON user_groups (group_id);
CREATE INDEX user_groups_prime_user_idx ON user_groups (user_id);

--
-- Indexes for table groups_big
--
CREATE UNIQUE INDEX groups_big_name_idx ON groups_big (group_name);

--
-- Indexes for table user_groups_big
--
CREATE UNIQUE INDEX user_groups_big_name_idx ON user_groups_big (group_name, user_id);
CREATE INDEX user_groups_big_idx ON user_groups (group_id);
CREATE INDEX user_groups_big_user_idx ON user_groups (user_id);

--
-- Indexes for table group_links
--
CREATE UNIQUE INDEX group_link_idx ON group_links (group_id, phrase_id);
CREATE INDEX group_link_phrase_idx ON group_links (phrase_id);

--
-- Indexes for table user_group_link
--
CREATE UNIQUE INDEX user_group_link_idx ON user_group_links (group_id, phrase_id, user_id);
CREATE INDEX user_group_link_phrase_idx ON user_group_links (phrase_id, user_id);

--
-- Indexes for table groups_prime_link
--
CREATE UNIQUE INDEX groups_prime_link_idx ON group_prime_links (group_id, phrase_id);
CREATE INDEX groups_prime_link_phrase_idx ON group_prime_links (phrase_id);

--
-- Indexes for prime user group links
--
CREATE UNIQUE INDEX user_groups_prime_link_idx ON user_group_prime_links (group_id, phrase_id, user_id);
CREATE INDEX user_groups_prime_link_phrase_idx ON user_group_prime_links (phrase_id, user_id);

--
-- Indexes for big group links
--
CREATE UNIQUE INDEX groups_big_link_idx ON group_big_links (group_id, phrase_id);
CREATE INDEX groups_big_link_phrase_idx ON group_big_links (phrase_id);

--
-- Indexes for big user group links
--
CREATE UNIQUE INDEX user_groups_big_link_idx ON user_group_big_links (group_id, phrase_id, user_id);
CREATE INDEX user_groups_big_link_phrase_idx ON user_group_big_links (phrase_id, user_id);

-- --------------------------------------------------------

--
-- indexes for table sources
--
CREATE INDEX sources_user_idx        ON sources (user_id);
CREATE INDEX sources_source_name_idx ON sources (source_name);
CREATE INDEX sources_source_type_idx ON sources (source_type_id);
--
-- indexes for table user_sources
--
ALTER TABLE user_sources ADD CONSTRAINT user_sources_pkey PRIMARY KEY (source_id,user_id);
CREATE INDEX user_sources_source_idx      ON user_sources (source_id);
CREATE INDEX user_sources_user_idx        ON user_sources (user_id);
CREATE INDEX user_sources_source_name_idx ON user_sources (source_name);
CREATE INDEX user_sources_source_type_idx ON user_sources (source_type_id);

--
-- Indexes for table source_values
--
CREATE INDEX source_value_group_idx ON source_values (group_id);
CREATE INDEX source_value_source_idx ON source_values (source_id);
CREATE INDEX source_value_user_idx ON source_values (user_id);

--
-- Indexes for table refs
--
CREATE UNIQUE INDEX ref_phrase_type_idx ON refs (phrase_id, ref_type_id);
CREATE INDEX ref_type_idx ON refs (ref_type_id);

--
-- Indexes for table user_refs
--
ALTER TABLE user_refs ADD CONSTRAINT user_ref_pkey PRIMARY KEY (ref_id, user_id);
CREATE INDEX user_ref_user_idx ON user_refs (user_id);
CREATE INDEX user_ref_idx ON user_refs (ref_id);

-- --------------------------------------------------------

--
-- Indexes for table values_standard_prime
--
CREATE INDEX values_standard_prime_source_idx ON values_standard_prime (source_id);

--
-- Indexes for table values_standard
--
CREATE INDEX values_standard_source_idx ON values_standard (source_id);

--
-- Indexes for table values
--
CREATE INDEX value_user_idx ON "values" (user_id);
CREATE INDEX value_source_idx ON "values" (source_id);

--
-- Indexes for table user_values
--
ALTER TABLE user_values ADD CONSTRAINT user_value_pkey PRIMARY KEY (group_id, user_id);
CREATE INDEX user_value_user_idx ON user_values (user_id);
CREATE INDEX user_value_source_idx ON user_values (source_id);

--
-- Indexes for table values_prime
--
CREATE INDEX values_prime_user_idx ON values_prime (user_id);
CREATE INDEX values_prime_source_idx ON values_prime (source_id);

--
-- Indexes for table user_values_prime
--
ALTER TABLE user_values_prime ADD CONSTRAINT user_values_prime_pkey PRIMARY KEY (group_id, user_id);
CREATE INDEX user_values_prime_user_idx ON user_values_prime (user_id);
CREATE INDEX user_values_prime_source_idx ON user_values_prime (source_id);

--
-- Indexes for table values_big
--
CREATE INDEX values_big_user_idx ON values_big (user_id);
CREATE INDEX values_big_source_idx ON values_big (source_id);

--
-- Indexes for table user_values_big
--
ALTER TABLE user_values_big ADD CONSTRAINT user_values_big_pkey PRIMARY KEY (group_id, user_id);
CREATE INDEX user_values_big_user_idx ON user_values_big (user_id);
CREATE INDEX user_values_big_source_idx ON user_values_big (source_id);

--
-- Indexes for table value_text_standard_prime
--
CREATE INDEX value_text_standard_prime_source_idx ON values_text_standard_prime (source_id);

--
-- Indexes for table value_text_standard
--
CREATE INDEX value_text_standard_source_idx ON value_text_standard (source_id);

--
-- Indexes for table value_text
--
CREATE INDEX value_text_user_idx ON value_text (user_id);
CREATE INDEX value_text_source_idx ON value_text (source_id);

--
-- Indexes for table user_value_text
--
ALTER TABLE user_value_text ADD CONSTRAINT user_value_text_pkey PRIMARY KEY (group_id, user_id);
CREATE INDEX user_value_text_user_idx ON user_value_text (user_id);
CREATE INDEX user_value_text_source_idx ON user_value_text (source_id);

--
-- Indexes for table value_text_prime
--
CREATE INDEX value_text_prime_user_idx ON value_text_prime (user_id);
CREATE INDEX value_text_prime_source_idx ON value_text_prime (source_id);

--
-- Indexes for table user_value_text_prime
--
ALTER TABLE user_value_text_prime ADD CONSTRAINT user_value_text_prime_pkey PRIMARY KEY (group_id, user_id);
CREATE INDEX user_value_text_prime_user_idx ON user_value_text_prime (user_id);
CREATE INDEX user_value_text_prime_source_idx ON user_value_text_prime (source_id);

--
-- Indexes for table value_text_big
--
CREATE INDEX value_text_big_user_idx ON value_text_big (user_id);
CREATE INDEX value_text_big_source_idx ON value_text_big (source_id);

--
-- Indexes for table user_value_text_big
--
ALTER TABLE user_value_text_big ADD CONSTRAINT user_value_text_big_pkey PRIMARY KEY (group_id, user_id);
CREATE INDEX user_value_text_big_user_idx ON user_value_text_big (user_id);
CREATE INDEX user_value_text_big_source_idx ON user_value_text_big (source_id);

--
-- Indexes for table value_time_standard_prime
--
CREATE INDEX value_time_standard_prime_source_idx ON value_time_standard_prime (source_id);

--
-- Indexes for table value_time_standard
--
CREATE INDEX value_time_standard_source_idx ON value_time_standard (source_id);

--
-- Indexes for table value_time
--
CREATE INDEX value_time_user_idx ON value_time (user_id);
CREATE INDEX value_time_source_idx ON value_time (source_id);

--
-- Indexes for table user_value_time
--
ALTER TABLE user_value_time ADD CONSTRAINT user_value_time_pkey PRIMARY KEY (group_id, user_id);
CREATE INDEX user_value_time_user_idx ON user_value_time (user_id);
CREATE INDEX user_value_time_source_idx ON user_value_time (source_id);

--
-- Indexes for table value_time_prime
--
CREATE INDEX value_time_prime_user_idx ON value_time_prime (user_id);
CREATE INDEX value_time_prime_source_idx ON value_time_prime (source_id);

--
-- Indexes for table user_value_time_prime
--
ALTER TABLE user_value_time_prime ADD CONSTRAINT user_value_time_prime_pkey PRIMARY KEY (group_id, user_id);
CREATE INDEX user_value_time_prime_user_idx ON user_value_time_prime (user_id);
CREATE INDEX user_value_time_prime_source_idx ON user_value_time_prime (source_id);

--
-- Indexes for table value_time_big
--
CREATE INDEX value_time_big_user_idx ON value_time_big (user_id);
CREATE INDEX value_time_big_source_idx ON value_time_big (source_id);

--
-- Indexes for table user_value_time_big
--
ALTER TABLE user_value_time_big ADD CONSTRAINT user_value_time_big_pkey PRIMARY KEY (group_id, user_id);
CREATE INDEX user_value_time_big_user_idx ON user_value_time_big (user_id);
CREATE INDEX user_value_time_big_source_idx ON user_value_time_big (source_id);

--
-- Indexes for table value_geo_standard_prime
--
CREATE INDEX value_geo_standard_prime_source_idx ON value_geo_standard_prime (source_id);

--
-- Indexes for table value_geo_standard
--
CREATE INDEX value_geo_standard_source_idx ON value_geo_standard (source_id);

--
-- Indexes for table value_geo
--
CREATE INDEX value_geo_user_idx ON value_geo (user_id);
CREATE INDEX value_geo_source_idx ON value_geo (source_id);

--
-- Indexes for table user_value_geo
--
ALTER TABLE user_value_geo ADD CONSTRAINT user_value_geo_pkey PRIMARY KEY (group_id, user_id);
CREATE INDEX user_value_geo_user_idx ON user_value_geo (user_id);
CREATE INDEX user_value_geo_source_idx ON user_value_geo (source_id);

--
-- Indexes for table value_geo_prime
--
CREATE INDEX value_geo_prime_user_idx ON value_geo_prime (user_id);
CREATE INDEX value_geo_prime_source_idx ON value_geo_prime (source_id);

--
-- Indexes for table user_value_geo_prime
--
ALTER TABLE user_value_geo_prime ADD CONSTRAINT user_value_geo_prime_pkey PRIMARY KEY (group_id, user_id);
CREATE INDEX user_value_geo_prime_user_idx ON user_value_geo_prime (user_id);
CREATE INDEX user_value_geo_prime_source_idx ON user_value_geo_prime (source_id);

--
-- Indexes for table value_geo_big
--
CREATE INDEX value_geo_big_user_idx ON value_geo_big (user_id);
CREATE INDEX value_geo_big_source_idx ON value_geo_big (source_id);

--
-- Indexes for table user_value_geo_big
--
ALTER TABLE user_value_geo_big ADD CONSTRAINT user_value_geo_big_pkey PRIMARY KEY (group_id, user_id);
CREATE INDEX user_value_geo_big_user_idx ON user_value_geo_big (user_id);
CREATE INDEX user_value_geo_big_source_idx ON user_value_geo_big (source_id);

-- --------------------------------------------------------

--
-- Indexes for table value_ts_data
--
CREATE UNIQUE INDEX value_time_series_idx ON value_ts_data (value_time_series_id, val_time);

-- --------------------------------------------------------

--
-- Indexes for table formula_elements
--
CREATE INDEX formula_element_idx ON formula_elements (formula_id);
CREATE INDEX formula_element_type_idx ON formula_elements (formula_element_type_id);

--
-- Indexes for table formulas
--
CREATE UNIQUE INDEX formula_name_idx ON formulas (formula_name);
CREATE INDEX formula_user_idx ON formulas (user_id);
CREATE INDEX formula_type_idx ON formulas (formula_type_id);

--
-- Indexes for table user_formulas
--
CREATE UNIQUE INDEX user_formula_unique_idx ON user_formulas (formula_id, user_id);
CREATE INDEX user_formula_idx ON user_formulas (formula_id);
CREATE INDEX user_formula_user_idx ON user_formulas (user_id);
CREATE INDEX user_formula_type_idx ON user_formulas (formula_type_id);

--
-- Indexes for table formula_links
--
CREATE INDEX formula_link_user_idx ON formula_links (user_id);
CREATE INDEX formula_link_idx ON formula_links (formula_id);
CREATE INDEX formula_link_type_idx ON formula_links (link_type_id);

--
-- Indexes for table user_formula_links
--
CREATE UNIQUE INDEX user_formula_link_unique_idx ON user_formula_links (formula_link_id, user_id);
CREATE INDEX user_formula_link_idx ON user_formula_links (formula_link_id);
CREATE INDEX user_formula_link_user_idx ON user_formula_links (user_id);
CREATE INDEX user_formula_link_type_idx ON user_formula_links (link_type_id);

-- --------------------------------------------------------

--
-- Indexes for results
--
CREATE UNIQUE INDEX group_idx ON results (group_id, formula_id, source_group_id, user_id);
CREATE INDEX result_formula_idx ON results (formula_id);
CREATE INDEX result_source_idx ON results (source_group_id);
CREATE INDEX result_user_idx ON results (user_id);

CREATE UNIQUE INDEX result_prime_idx ON result_prime (group_id, formula_id, source_group_id, user_id);
CREATE INDEX result_prime_formula_idx ON result_prime (formula_id);
CREATE INDEX result_prime_source_idx ON result_prime (source_group_id);
CREATE INDEX result_prime_user_idx ON result_prime (user_id);

CREATE UNIQUE INDEX result_big_idx ON result_big (group_id, formula_id, source_group_id, user_id);
CREATE INDEX result_big_formula_idx ON result_big (formula_id);
CREATE INDEX result_big_source_idx ON result_big (source_group_id);
CREATE INDEX result_big_user_idx ON result_big (user_id);

-- --------------------------------------------------------

--
-- Indexes for table views
--
CREATE INDEX view_type_idx ON views (view_type_id);

--
-- Indexes for table user_views
--
ALTER TABLE user_views ADD CONSTRAINT user_view_pkey PRIMARY KEY (view_id, user_id);
CREATE INDEX user_view_user_idx ON user_views (user_id);
CREATE INDEX user_view_type_idx ON user_views (view_type_id);
CREATE INDEX user_view_idx ON user_views (view_id);

--
-- Indexes for table components
--
CREATE INDEX component_formula_idx ON components (formula_id);

--
-- Indexes for table user_components
--
ALTER TABLE user_components ADD CONSTRAINT user_component_pkey PRIMARY KEY (component_id, user_id);
CREATE INDEX user_component_user_idx ON user_components (user_id);
CREATE INDEX user_component_idx ON user_components (component_id);
CREATE INDEX user_component_type_idx ON user_components (component_type_id);

--
-- Indexes for table component_links
--
CREATE INDEX component_link_idx ON component_links (view_id);
CREATE INDEX component_link_component_idx ON component_links (component_id);
CREATE INDEX component_link_position__idx ON component_links (position_type);

--
-- Indexes for table user_component_links
--
ALTER TABLE user_component_links ADD CONSTRAINT user_component_link_pkey PRIMARY KEY (component_link_id, user_id);
CREATE INDEX user_component_link_user_idx ON user_component_links (user_id);
CREATE INDEX user_component_link_position_idx ON user_component_links (position_type);
CREATE INDEX user_component_link_view_idx ON user_component_links (component_link_id);

-- --------------------------------------------------------

--
-- foreign key constraints and auto_increment for tables
--

-- --------------------------------------------------------

--
-- constraints for table sys_log
--
ALTER TABLE sys_log
    ADD CONSTRAINT sys_log_fk_1 FOREIGN KEY (sys_log_type_id) REFERENCES sys_log_types (sys_log_type_id),
    ADD CONSTRAINT sys_log_fk_2 FOREIGN KEY (sys_log_status_id) REFERENCES sys_log_status (sys_log_status_id),
    ADD CONSTRAINT sys_log_fk_3 FOREIGN KEY (sys_log_function_id) REFERENCES sys_log_functions (sys_log_function_id);

--
-- constraints for table sys_script_times
--
ALTER TABLE sys_script_times
    ADD CONSTRAINT sys_script_times_fk_1 FOREIGN KEY (sys_script_id) REFERENCES sys_scripts (sys_script_id);

--
-- constraints for table calc_and_cleanup_tasks
--
ALTER TABLE calc_and_cleanup_tasks
    ADD CONSTRAINT calc_and_cleanup_tasks_fk_1 FOREIGN KEY (calc_and_cleanup_task_type_id) REFERENCES calc_and_cleanup_task_types (calc_and_cleanup_task_type_id);

--
-- constraints for table users
--
ALTER TABLE users
    ADD CONSTRAINT users_fk_1 FOREIGN KEY (user_type_id) REFERENCES user_types (user_type_id),
    ADD CONSTRAINT users_fk_2 FOREIGN KEY (user_profile_id) REFERENCES user_profiles (profile_id);

--
-- constraints for table change_fields
--
ALTER TABLE change_fields
    ADD CONSTRAINT change_fields_fk_1 FOREIGN KEY (table_id) REFERENCES change_tables (change_table_id);

--
-- constraints for table changes
--
ALTER TABLE changes
    ADD CONSTRAINT changes_fk_1 FOREIGN KEY (change_action_id) REFERENCES change_actions (change_action_id),
    ADD CONSTRAINT changes_fk_2 FOREIGN KEY (change_field_id) REFERENCES change_fields (change_field_id);

--
-- constraints for table change_links
--
ALTER TABLE change_links
    ADD CONSTRAINT change_links_fk_1 FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE NO ACTION,
    ADD CONSTRAINT change_links_fk_2 FOREIGN KEY (change_table_id) REFERENCES change_tables (change_table_id),
    ADD CONSTRAINT change_links_fk_3 FOREIGN KEY (change_action_id) REFERENCES change_actions (change_action_id);

--
-- constraints for table change_fields
--
ALTER TABLE language_forms
    ADD CONSTRAINT language_forms_fk_1 FOREIGN KEY (language_id) REFERENCES languages (language_id);

--
-- constraints for table words
--
ALTER TABLE words
    ADD CONSTRAINT word_name UNIQUE (word_name);
ALTER TABLE words
    ADD CONSTRAINT words_fk_1 FOREIGN KEY (view_id) REFERENCES views (view_id),
    ADD CONSTRAINT words_fk_2 FOREIGN KEY (phrase_type_id) REFERENCES phrase_types (phrase_type_id);

--
-- constraints for table user_words
--
ALTER TABLE user_words
    ADD CONSTRAINT user_words_fk_1 FOREIGN KEY (user_id) REFERENCES users (user_id),
    ADD CONSTRAINT user_words_fk_2 FOREIGN KEY (phrase_type_id) REFERENCES phrase_types (phrase_type_id),
    ADD CONSTRAINT user_words_fk_3 FOREIGN KEY (view_id) REFERENCES views (view_id),
    ADD CONSTRAINT user_words_fk_4 FOREIGN KEY (word_id) REFERENCES words (word_id);

--
-- constraints for table word_periods
--
ALTER TABLE word_periods
    ADD CONSTRAINT word_periods_fk_1 FOREIGN KEY (word_id) REFERENCES words (word_id);

--
-- constraints for table triples
--
ALTER TABLE triples
    ADD CONSTRAINT triple_name UNIQUE (triple_name);
ALTER TABLE triples
    ADD CONSTRAINT triples_fk_2 FOREIGN KEY (verb_id) REFERENCES verbs (verb_id),
    ADD CONSTRAINT triples_fk_4 FOREIGN KEY (view_id) REFERENCES views (view_id),
    ADD CONSTRAINT triples_fk_5 FOREIGN KEY (phrase_type_id) REFERENCES phrase_types (phrase_type_id);

-- ADD CONSTRAINT triples_fk_1 FOREIGN KEY (from_phrase_id) REFERENCES phrases (phrase_id),
-- ADD CONSTRAINT triples_fk_3 FOREIGN KEY (to_phrase_id) REFERENCES phrases (phrase_id),

--
-- constraints for table user_triples
--
ALTER TABLE user_triples
    ADD CONSTRAINT user_triples_fk_1 FOREIGN KEY (triple_id) REFERENCES triples (triple_id),
    ADD CONSTRAINT user_triples_fk_2 FOREIGN KEY (user_id) REFERENCES users (user_id);

--
-- constraints for table user_groups
--
ALTER TABLE user_groups
    ADD CONSTRAINT user_groups_fk_1 FOREIGN KEY (group_id) REFERENCES groups (group_id),
    ADD CONSTRAINT user_groups_fk_2 FOREIGN KEY (user_id) REFERENCES users (user_id);

--
-- constraints for table user_groups_prime
--
ALTER TABLE user_groups_prime
    ADD CONSTRAINT user_groups_prime_fk_1 FOREIGN KEY (group_id) REFERENCES groups_prime (group_id),
    ADD CONSTRAINT user_groups_prime_fk_2 FOREIGN KEY (user_id) REFERENCES users (user_id);

--
-- constraints for table user_groups_big
--
ALTER TABLE user_groups_big
    ADD CONSTRAINT user_groups_big_fk_1 FOREIGN KEY (group_id) REFERENCES groups_big (group_id),
    ADD CONSTRAINT user_groups_big_fk_2 FOREIGN KEY (user_id) REFERENCES users (user_id);

--
-- constraints for table group_links
--
-- ALTER TABLE group_link
--     ADD CONSTRAINT group_link_fk_1 FOREIGN KEY (phrase_id) REFERENCES phrases (phrase_id);

--
-- constraints for table group_links
--
ALTER TABLE user_group_links
    ADD CONSTRAINT user_group_link_fk_1 FOREIGN KEY (user_id) REFERENCES users (user_id);
--    ADD CONSTRAINT user_group_link_fk_2 FOREIGN KEY (phrase_id) REFERENCES phrases (phrase_id);

--
-- constraints for table groups_prime_link
--
-- ALTER TABLE groups_prime_link
--    ADD CONSTRAINT groups_prime_link_fk_1 FOREIGN KEY (phrase_id) REFERENCES phrases (phrase_id);

--
-- constraints for table user_groups_prime_link
--
ALTER TABLE user_group_prime_links
    ADD CONSTRAINT user_group_prime_links_fk_1 FOREIGN KEY (user_id) REFERENCES users (user_id);
--  ADD CONSTRAINT user_group_prime_links_fk_2 FOREIGN KEY (phrase_id) REFERENCES phrases (phrase_id);

--
-- constraints for table groups_big_link
--
-- ALTER TABLE groups_big_link
--    ADD CONSTRAINT groups_big_link_fk_1 FOREIGN KEY (phrase_id) REFERENCES phrases (phrase_id);

--
-- constraints for table user_groups_big_link
--
ALTER TABLE user_group_big_links
    ADD CONSTRAINT user_group_big_links_fk_1 FOREIGN KEY (user_id) REFERENCES users (user_id);
--    ADD CONSTRAINT user_group_big_links_fk_2 FOREIGN KEY (phrase_id) REFERENCES phrases (phrase_id);

--
-- constraints for table sources
--
ALTER TABLE sources
    ADD CONSTRAINT sources_fk_1 FOREIGN KEY (source_type_id) REFERENCES source_types (source_type_id);

--
-- constraints for table user_sources
--
ALTER TABLE user_sources
    ADD CONSTRAINT user_sources_fk_1 FOREIGN KEY (source_id) REFERENCES sources (source_id),
    ADD CONSTRAINT user_sources_fk_2 FOREIGN KEY (user_id) REFERENCES users (user_id);

--
-- constraints for table refs
--
ALTER TABLE refs
    ADD CONSTRAINT refs_fk_1 FOREIGN KEY (ref_type_id) REFERENCES ref_types (ref_type_id);

--
-- constraints for table user_refs
--
ALTER TABLE user_refs
    ADD CONSTRAINT user_refs_fk_1 FOREIGN KEY (ref_id) REFERENCES refs (ref_id),
    ADD CONSTRAINT user_refs_fk_2 FOREIGN KEY (user_id) REFERENCES users (user_id);

--
-- constraints for table source_values
--
ALTER TABLE source_values
    ADD CONSTRAINT source_values_fk_1 FOREIGN KEY (user_id) REFERENCES users (user_id),
    ADD CONSTRAINT source_values_fk_2 FOREIGN KEY (source_id) REFERENCES sources (source_id);

--
-- constraints for table values_standard_prime
--
ALTER TABLE values_standard_prime
    ADD CONSTRAINT values_standard_prime_fk_1 FOREIGN KEY (source_id) REFERENCES sources (source_id);

--
-- constraints for table values_standard
--
ALTER TABLE values_standard
    ADD CONSTRAINT values_standard_fk_1 FOREIGN KEY (source_id) REFERENCES sources (source_id);

--
-- constraints for table values
--
ALTER TABLE values
    ADD CONSTRAINT values_fk_1 FOREIGN KEY (user_id) REFERENCES users (user_id),
    ADD CONSTRAINT values_fk_2 FOREIGN KEY (source_id) REFERENCES sources (source_id),
    ADD CONSTRAINT values_fk_3 FOREIGN KEY (group_id) REFERENCES groups (group_id),
    ADD CONSTRAINT values_fk_4 FOREIGN KEY (protect_id) REFERENCES protection_types (protection_type_id);

--
-- constraints for table user_values
--
ALTER TABLE user_values
    ADD CONSTRAINT user_values_fk_1 FOREIGN KEY (user_id) REFERENCES users (user_id),
    ADD CONSTRAINT user_values_fk_2 FOREIGN KEY (source_id) REFERENCES sources (source_id),
    ADD CONSTRAINT user_values_fk_3 FOREIGN KEY (share_type_id) REFERENCES share_types (share_type_id),
    ADD CONSTRAINT user_values_fk_4 FOREIGN KEY (protect_id) REFERENCES protection_types (protection_type_id);

--
-- constraints for table user_value_time_series
--
ALTER TABLE user_value_time_series
    ADD CONSTRAINT user_value_time_series_fk_1 FOREIGN KEY (user_id) REFERENCES users (user_id),
    ADD CONSTRAINT user_value_time_series_fk_2 FOREIGN KEY (source_id) REFERENCES sources (source_id),
    ADD CONSTRAINT user_value_time_series_fk_3 FOREIGN KEY (share_type_id) REFERENCES share_types (share_type_id),
    ADD CONSTRAINT user_value_time_series_fk_4 FOREIGN KEY (protect_id) REFERENCES protection_types (protection_type_id);

--
-- constraints for table formulas
--
ALTER TABLE formulas
    ADD CONSTRAINT formulas_fk_1 FOREIGN KEY (formula_type_id) REFERENCES formula_types (formula_type_id),
    ADD CONSTRAINT formulas_fk_2 FOREIGN KEY (user_id) REFERENCES users (user_id),
    ADD CONSTRAINT formulas_fk_3 FOREIGN KEY (protect_id) REFERENCES protection_types (protection_type_id);

--
-- constraints for table user_formulas
--
ALTER TABLE user_formulas
    ADD CONSTRAINT user_formulas_fk_4 FOREIGN KEY (share_type_id) REFERENCES share_types (share_type_id),
    ADD CONSTRAINT user_formulas_fk_1 FOREIGN KEY (user_id) REFERENCES users (user_id),
    ADD CONSTRAINT user_formulas_fk_2 FOREIGN KEY (formula_type_id) REFERENCES formula_types (formula_type_id),
    ADD CONSTRAINT user_formulas_fk_3 FOREIGN KEY (formula_id) REFERENCES formulas (formula_id);

--
-- constraints for table formula_elements
--
ALTER TABLE formula_elements
    ADD CONSTRAINT formula_elements_fk_1 FOREIGN KEY (formula_element_type_id) REFERENCES formula_element_types (formula_element_type_id),
    ADD CONSTRAINT formula_elements_fk_2 FOREIGN KEY (formula_id) REFERENCES formulas (formula_id);

--
-- constraints for table formula_links
--
ALTER TABLE formula_links
    ADD CONSTRAINT formula_links_fk_1 FOREIGN KEY (user_id) REFERENCES users (user_id);

--
-- constraints for table user_formula_links
--
ALTER TABLE user_formula_links
    ADD CONSTRAINT user_formula_links_fk_1 FOREIGN KEY (formula_link_id) REFERENCES formula_links (formula_link_id),
    ADD CONSTRAINT user_formula_links_fk_2 FOREIGN KEY (user_id) REFERENCES users (user_id),
    ADD CONSTRAINT user_formula_links_fk_3 FOREIGN KEY (link_type_id) REFERENCES formula_link_types (formula_link_type_id);

--
-- constraints for table results
--
ALTER TABLE results
    ADD CONSTRAINT results_fk_1 FOREIGN KEY (formula_id) REFERENCES formulas (formula_id),
    ADD CONSTRAINT results_fk_2 FOREIGN KEY (user_id) REFERENCES users (user_id);

--
-- constraints for table user_views
--
ALTER TABLE user_views
    ADD CONSTRAINT user_views_fk_1 FOREIGN KEY (user_id) REFERENCES users (user_id),
    ADD CONSTRAINT user_views_fk_2 FOREIGN KEY (view_type_id) REFERENCES view_types (view_type_id),
    ADD CONSTRAINT user_views_fk_3 FOREIGN KEY (view_id) REFERENCES views (view_id);

--
-- constraints for table components
--
ALTER TABLE components
    ADD CONSTRAINT components_fk_2 FOREIGN KEY (formula_id) REFERENCES formulas (formula_id);

--
-- constraints for table user_components
--
ALTER TABLE user_components
    ADD CONSTRAINT user_components_fk_1 FOREIGN KEY (user_id) REFERENCES users (user_id),
    ADD CONSTRAINT user_components_fk_2 FOREIGN KEY (component_id) REFERENCES components (component_id),
    ADD CONSTRAINT user_components_fk_3 FOREIGN KEY (component_type_id) REFERENCES component_types (component_type_id);

--
-- constraints for table component_links
--
ALTER TABLE component_links
    ADD CONSTRAINT component_links_fk_1 FOREIGN KEY (view_id) REFERENCES views (view_id),
    ADD CONSTRAINT component_links_fk_2 FOREIGN KEY (position_type) REFERENCES component_position_types (component_position_type_id),
    ADD CONSTRAINT component_links_fk_3 FOREIGN KEY (component_id) REFERENCES components (component_id);

--
-- constraints for table user_component_links
--
ALTER TABLE user_component_links
    ADD CONSTRAINT user_component_links_fk_1 FOREIGN KEY (user_id) REFERENCES users (user_id),
    ADD CONSTRAINT user_component_links_fk_2 FOREIGN KEY (component_link_id) REFERENCES component_links (component_link_id),
    ADD CONSTRAINT user_component_links_fk_3 FOREIGN KEY (position_type) REFERENCES component_position_types (component_position_type_id);


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
