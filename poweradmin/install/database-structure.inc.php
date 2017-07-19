<?php

$def_tables = [
	[
		'table_name' => 'perm_items',
		'options' => ['type' => 'innodb'],
		'fields' => [
			'id' => [
                'type' => 'integer',
                'notnull' => 1,
                'unsigned' => 0,
                'autoincrement' => 1,
                'name' => 'id',
                'table' => 'perm_items',
                'flags' => 'primary_keynot_null'
			],
			'name' => [
                'type' => 'text',
                'notnull' => 1,
                'length' => 64,
                'fixed' => 0,
                'default' => 0,
                'name' => 'name',
                'table' => 'perm_items',
                'flags' => 'not_null'
			],
			'descr' => [
                'type' => 'text',
                'length' => 1024,
                'notnull' => 1,
                'fixed' => 0,
                'default' => 0,
                'name' => 'descr',
                'table' => 'perm_items',
                'flags' => 'not_null'
			]
		]
	],
	[
		'table_name' => 'perm_templ',
		'options' => ['type' => 'innodb'],
		'fields' => [
			'id' => [
                'type' => 'integer',
                'notnull' => 1,
                'unsigned' => 0,
                'default' => 0,
                'autoincrement' => 1,
                'name' => 'id',
                'table' => 'perm_templ',
                'flags' => 'primary_keynot_null'
			],
			'name' => [
                'type' => 'text',
                'notnull' => 1,
                'length' => 128,
                'fixed' => 0,
                'default' => 0,
                'name' => 'name',
                'table' => 'perm_templ',
                'flags' => 'not_null'
			],
			'descr' => [
                'notnull' => 1,
                'fixed' => 0,
                'default' => 0,
                'type' => 'text',
                'length' => 1024,
                'name' => 'descr',
                'table' => 'perm_templ',
                'flags' => 'not_null'
			]
		]
	],
	[
		'table_name' => 'perm_templ_items',
		'options' => ['type' => 'innodb'],
		'fields' => [
			'id' => [
                'notnull' => 1,
                'unsigned' => 0,
                'default' => 0,
                'autoincrement' => 1,
                'type' => 'integer',
                'name' => 'id',
                'table' => 'perm_templ_items',
                'flags' => 'primary_keynot_null'
			],
			'templ_id' => [
                'notnull' => 1,
                'length' => 4,
                'unsigned' => 0,
                'default' => 0,
                'type' => 'integer',
                'name' => 'templ_id',
                'table' => 'perm_templ_items',
                'flags' => 'not_null'
			],
			'perm_id' => [
                'notnull' => 1,
                'length' => 4,
                'unsigned' => 0,
                'default' => 0,
                'type' => 'integer',
                'name' => 'perm_id',
                'table' => 'perm_templ_items',
                'flags' => 'not_null'
			]
		]
	],
	[
		'table_name' => 'users',
		'options' => ['type' => 'innodb'],
		'fields' => [
			'id' => [
                'notnull' => 1,
                'unsigned' => 0,
                'default' => 0,
                'autoincrement' => 1,
                'type' => 'integer',
                'name' => 'id',
                'table' => 'users',
                'flags' => 'primary_keynot_null'],
			'username' => [
                'notnull' => 1,
                'length' => 64,
                'fixed' => 0,
                'default' => 0,
                'type' => 'text',
                'name' => 'username',
                'table' => 'users',
                'flags' => 'not_null'],
			'password' => [
                'notnull' => 1,
                'length' => 128,
                'fixed' => 0,
                'default' => 0,
                'type' => 'text',
                'name' => 'password',
                'table' => 'users',
                'flags' => 'not_null'],
			'fullname' => [
                'notnull' => 1,
                'length' => 255,
                'fixed' => 0,
                'default' => 0,
                'type' => 'text',
                'name' => 'fullname',
                'table' => 'users',
                'flags' => 'not_null'],
			'email' => [
                'notnull' => 1,
                'length' => 255,
                'fixed' => 0,
                'default' => 0,
                'type' => 'text',
                'name' => 'email',
                'table' => 'users',
                'flags' => 'not_null'],
			'description' => [
                'notnull' => 1,
                'fixed' => 0,
                'default' => 0,
                'type' => 'text',
                'length' => 1024,
                'name' => 'description',
                'table' => 'users',
                'flags' => 'not_null'],
			'perm_templ' => [
                'notnull' => 1,
                'length' => 1,
                'unsigned' => 0,
                'default' => 0,
                'type' => 'integer',
                'name' => 'perm_templ',
                'table' => 'users',
                'flags' => 'not_null'],
			'active' => [
                'notnull' => 1,
                'length' => 1,
                'unsigned' => 0,
                'default' => 0,
                'type' => 'integer',
                'name' => 'active',
                'table' => 'users',
                'flags' => 'not_null'],
			'use_ldap' => [
                'notnull' => 1,
                'length' => 1,
                'unsigned' => 0,
                'default' => 0,
                'type' => 'integer',
                'name' => 'use_ldap',
                'table' => 'users',
                'flags' => 'not_null']
		]
	],
	[
		'table_name' => 'zones',
		'options' => ['type' => 'innodb'],
		'fields' => [
			'id' => [
                'notnull' => 1,
                'length' => 4,
                'unsigned' => 0,
                'default' => 0,
                'autoincrement' => 1,
                'type' => 'integer',
                'name' => 'id',
                'table' => 'zones',
                'flags' => 'primary_keynot_null'],
			'domain_id' => [
                'notnull' => 1,
                'length' => 4,
                'unsigned' => 0,
                'default' => 0,
                'type' => 'integer',
                'name' => 'domain_id',
                'table' => 'zones',
                'flags' => 'not_null'],
			'owner' => [
                'notnull' => 1,
                'length' => 4,
                'unsigned' => 0,
                'default' => 0,
                'type' => 'integer',
                'name' => 'owner',
                'table' => 'zones',
                'flags' => 'not_null'],
			'comment' => [
                'notnull' => 0,
                'length' => 1024,
                'fixed' => 0,
                'default' => 0,
                'type' => 'text',
                'name' => 'comment',
                'table' => 'zones',
                'flags' => ''],
			'zone_templ_id' => [
                'notnull' => 1,
                'length' => 4,
                'unsigned' => 0,
                'type' => 'integer',
                'name' => 'zone_templ_id',
                'table' => 'zones',
                'flags' => ''],
		]
	],
	[
		'table_name' => 'zone_templ',
		'options' => ['type' => 'innodb'],
		'fields' => [
			'id' => [
                'notnull' => 1,
                'length' => 11,
                'unsigned' => 0,
                'default' => 0,
                'autoincrement' => 1,
                'type' => 'integer',
                'name' => 'id',
                'table' => 'zone_templ',
                'flags' => 'primary_keynot_null'],
			'name' => [
                'notnull' => 1,
                'length' => 128,
                'fixed' => 0,
                'default' => 0,
                'type' => 'text',
                'name' => 'name',
                'table' => 'zone_templ',
                'flags' => 'not_null'],
			'descr' => [
                'notnull' => 1,
                'length' => 1024,
                'fixed' => 0,
                'default' => 0,
                'type' => 'text',
                'name' => 'descr',
                'table' => 'zone_templ',
                'flags' => 'not_null'],
			'owner' => [
                'notnull' => 1,
                'length' => 11,
                'fixed' => 0,
                'default' => 0,
                'type' => 'integer',
                'name' => 'owner',
                'table' => 'zone_templ',
                'flags' => 'not_null']
		]
	],
	[
		'table_name' => 'zone_templ_records',
		'options' => ['type' => 'innodb'],
		'fields' => [
			'id' => [
                'notnull' => 1,
                'length' => 11,
                'unsigned' => 0,
                'default' => 0,
                'autoincrement' => 1,
                'type' => 'integer',
                'name' => 'id',
                'table' => 'zone_templ_records',
                'flags' => 'primary_keynot_null'],
			'zone_templ_id' => [
                'notnull' => 1,
                'length' => 11,
                'fixed' => 0,
                'default' => 0,
                'type' => 'integer',
                'name' => 'zone_templ_id',
                'table' => 'zone_templ_records',
                'flags' => 'not_null'],
			'name' => [
                'notnull' => 1,
                'length' => 255,
                'fixed' => 0,
                'default' => 0,
                'type' => 'text',
                'name' => 'name',
                'table' => 'zone_templ_records',
                'flags' => ''],
			'type' => [
                'notnull' => 1,
                'length' => 6,
                'fixed' => 0,
                'default' => 0,
                'type' => 'text',
                'name' => 'type',
                'table' => 'zone_templ_records',
                'flags' => ''],
			'content' => [
                'notnull' => 1,
                'length' => 255,
                'fixed' => 0,
                'default' => 0,
                'type' => 'text',
                'name' => 'content',
                'table' => 'zone_templ_records',
                'flags' => ''],
			'ttl' => [
                'notnull' => 1,
                'length' => 11,
                'fixed' => 0,
                'default' => 0,
                'type' => 'integer',
                'name' => 'ttl',
                'table' => 'zone_templ_records',
                'flags' => ''],
			'prio' => [
                'notnull' => 1,
                'length' => 11,
                'fixed' => 0,
                'default' => 0,
                'type' => 'integer',
                'name' => 'prio',
                'table' => 'zone_templ_records',
                'flags' => '']
		]
	],
	[
		'table_name' => 'records_zone_templ',
		'options' => ['type' => 'innodb'],
		'fields' => [
			'domain_id' => [
                'notnull' => 1,
                'length' => 11,
                'fixed' => 0,
                'default' => 0,
                'type' => 'integer',
                'name' => 'domain_id',
                'table' => 'records_zone_templ',
                'flags' => 'not_null'],
			'record_id' => [
                'notnull' => 1,
                'length' => 11,
                'fixed' => 0,
                'default' => 0,
                'type' => 'integer',
                'name' => 'record_id',
                'table' => 'records_zone_templ',
                'flags' => 'not_null'],
			'zone_templ_id' => [
                'notnull' => 1,
                'length' => 11,
                'fixed' => 0,
                'default' => 0,
                'type' => 'integer',
                'name' => 'zone_templ_id',
                'table' => 'records_zone_templ',
                'flags' => 'not_null']
		]
	],
	[
		'table_name' => 'migrations',
		'options' => ['type' => 'innodb'],
		'fields' => [
			'domain_id' => [
                'notnull' => 1,
                'length' => 255,
                'fixed' => 0,
                'default' => 0,
                'type' => 'text',
                'name' => 'version',
                'table' => 'migrations',
                'flags' => 'not_null'],
			'record_id' => [
                'notnull' => 1,
                'length' => 11,
                'fixed' => 0,
                'default' => 0,
                'type' => 'integer',
                'name' => 'apply_time',
                'table' => 'migrations',
                'flags' => 'not_null']
		]
	]
];

// Tables from PowerDNS
$grantTables = ['supermasters', 'domains', 'records'];
// Include PowerAdmin tables
foreach ($def_tables as $table) {
    $grantTables[] = $table['table_name'];
}

// For PostgreSQL you need to grant access to sequences
$grantSequences = ['domains_id_seq', 'records_id_seq'];
foreach ($def_tables as $table) {
    // ignore tables without primary key
    if ($table['table_name'] == 'migrations') { continue; }
    if ($table['table_name'] == 'records_zone_templ') { continue; }
    $grantSequences[] = $table['table_name'] . '_id_seq';
}

$def_permissions = [
	[41, 'zone_master_add', 'User is allowed to add new master zones.'],
	[42, 'zone_slave_add', 'User is allowed to add new slave zones.'],
	[43, 'zone_content_view_own', 'User is allowed to see the content and meta data of zones he owns.'],
	[44, 'zone_content_edit_own', 'User is allowed to edit the content of zones he owns.'],
	[45, 'zone_meta_edit_own', 'User is allowed to edit the meta data of zones he owns.'],
	[46, 'zone_content_view_others', 'User is allowed to see the content and meta data of zones he does not own.'],
	[47, 'zone_content_edit_others', 'User is allowed to edit the content of zones he does not own.'],
	[48, 'zone_meta_edit_others', 'User is allowed to edit the meta data of zones he does not own.'],
	[49, 'search', 'User is allowed to perform searches.'],
	[50, 'supermaster_view', 'User is allowed to view supermasters.'],
	[51, 'supermaster_add', 'User is allowed to add new supermasters.'],
	[52, 'supermaster_edit', 'User is allowed to edit supermasters.'],
	[53, 'user_is_ueberuser', 'User has full access. God-like. Redeemer.'],
	[54, 'user_view_others', 'User is allowed to see other users and their details.'],
	[55, 'user_add_new', 'User is allowed to add new users.'],
	[56, 'user_edit_own', 'User is allowed to edit their own details.'],
	[57, 'user_edit_others', 'User is allowed to edit other users.'],
	[58, 'user_passwd_edit_others', 'User is allowed to edit the password of other users.'], // not used
	[59, 'user_edit_templ_perm', 'User is allowed to change the permission template that is assigned to a user.'],
	[60, 'templ_perm_add', 'User is allowed to add new permission templates.'],
	[61, 'templ_perm_edit', 'User is allowed to edit existing permission templates.'],
	[62, 'zone_content_edit_own_as_client', 'User is allowed to edit record, but not SOA and NS.'],
];

$def_remaining_queries = [
    "INSERT INTO users (username, password, fullname, email, description, perm_templ, active, use_ldap) VALUES ('admin',%s,'Administrator','admin@example.net','Administrator with full rights.',1,1,0)",
    "INSERT INTO perm_templ (name, descr) VALUES ('Administrator','Administrator template with full rights.')",
    "INSERT INTO perm_templ_items (templ_id, perm_id) VALUES (1,53)"
];
