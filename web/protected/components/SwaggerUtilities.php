<?php
/**
 * This file is part of the DreamFactory Services Platform(tm) (DSP)
 *
 * DreamFactory Services Platform(tm) <http://github.com/dreamfactorysoftware/dsp-core>
 * Copyright 2012-2013 DreamFactory Software, Inc. <developer-support@dreamfactory.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * SwaggerUtilities
 * A utilities class to handle swagger documentation of the REST API.
 */
class SwaggerUtilities
{

	/**
	 * Swagger base response used by Swagger-UI
	 *
	 * @param string $service
	 *
	 * @return array
	 */
	public static function getBaseInfo( $service = '' )
	{
		$swagger = array(
			'apiVersion'     => Versions::API_VERSION,
			'swaggerVersion' => '1.1',
			'basePath'       => Yii::app()->getRequest()->getHostInfo() . '/rest'
		);
		if ( !empty( $service ) )
		{
			$swagger['resourcePath'] = '/' . $service;
		}

		return $swagger;
	}

	/**
	 * Swagger base APIs used by Swagger-UI
	 *
	 * @param string $service
	 *
	 * @return array
	 */
	public static function getBaseApis( $service )
	{
		$apis = array(
			array(
				'path'        => '/' . $service,
				'description' => "Operations available for this service",
				'operations'  => array(
					array(
						"httpMethod"     => "GET",
						"summary"        => "List resources available for this service",
						"notes"          => "See listed operations for each resource available.",
						"responseClass"  => "Resources",
						"nickname"       => "getResources",
						"parameters"     => array(),
						"errorResponses" => static::getErrors( array( ErrorCodes::UNAUTHORIZED ) )
					)
				)
			),
		);

		return $apis;
	}

	/**
	 * Swagger base models used by Swagger-UI
	 *
	 * @return array
	 */
	public static function getBaseModels()
	{
		$models = array(
			"Resources" => array(
				"id" => "Resources",
				"properties" => array(
					"resource" => array(
						"type" => "array",
						"description" => "Resources available for this service",
						"items" => array( '$ref' => "Resource")
					)
				)
			),
			"Resource" => array(
				"id" => "Resource",
				"properties" => array(
					"name" => array(
						"type" => "string"
					)
				)
			),
			"Success" => array(
				"id" => "Success",
				"properties" => array(
					"success" => array(
						"type" => "boolean"
					)
				)
			),
		);

		return $models;
	}

	/**
	 * Swagger output for common error responses
	 *
	 * @param array   $codes
	 * @param boolean $include_common
	 *
	 * @return array
	 */
	public static function getErrors( $codes = array(), $include_common = true )
	{
		$swagger = array();

		if ( $include_common )
		{
			$swagger[] = array(
				"code"   => ErrorCodes::UNAUTHORIZED,
				"reason" => "Unauthorized Access - No currently valid session available."
			);
			$swagger[] = array(
				"code"   => ErrorCodes::INTERNAL_SERVER_ERROR,
				"reason" => "System Error - Specific reason is included in the error message."
			);
		}

		foreach ( $codes as $code )
		{
			switch ( $code )
			{
				case ErrorCodes::BAD_REQUEST:
					$swagger[] = array(
						"code"   => ErrorCodes::BAD_REQUEST,
						"reason" => "Invalid Request - Specific reason is included in the error message."
					);
					break;
				case ErrorCodes::UNAUTHORIZED:
					$swagger[] = array(
						"code"   => ErrorCodes::UNAUTHORIZED,
						"reason" => "Unauthorized Access - No currently valid session available."
					);
					break;
				case ErrorCodes::FORBIDDEN:
					$swagger[] = array(
						"code"   => ErrorCodes::FORBIDDEN,
						"reason" => "Forbidden Access - The current session denies permission for this action."
					);
					break;
				case ErrorCodes::NOT_FOUND:
					$swagger[] = array(
						"code"   => ErrorCodes::NOT_FOUND,
						"reason" => "Resource Not Found - No resource matching the identifiers given exist in the system."
					);
					break;
				case ErrorCodes::METHOD_NOT_ALLOWED:
					$swagger[] = array(
						"code"   => ErrorCodes::METHOD_NOT_ALLOWED,
						"reason" => "Action Not Allowed - This action is not allowed on this server."
					);
					break;
				case ErrorCodes::INTERNAL_SERVER_ERROR:
					$swagger[] = array(
						"code"   => ErrorCodes::INTERNAL_SERVER_ERROR,
						"reason" => "System Error - Specific reason is included in the error message."
					);
					break;
				case ErrorCodes::NOT_IMPLEMENTED:
					$swagger[] = array(
						"code"   => ErrorCodes::NOT_IMPLEMENTED,
						"reason" => "Not Implemented - This resource or action is not currently implemented."
					);
					break;
			}
		}
	}

	/**
	 * Swagger output for common api parameters
	 *
	 * @param        $parameters
	 * @param string $method
	 *
	 * @return array
	 */
	public static function getParameters( $parameters, $method = '' )
	{
		$swagger = array();
		foreach ( $parameters as $param )
		{
			switch ( $param )
			{
				case 'table_name':
					$swagger[] = array(
						"paramType"     => "path",
						"name"          => $param,
						"description"   => "Name of the table to perform operations on.",
						"dataType"      => "String",
						"required"      => true,
						"allowMultiple" => false
					);
					break;
				case 'field_name':
					$swagger[] = array(
						"paramType"     => "path",
						"name"          => $param,
						"description"   => "Name of the table field/column to perform operations on.",
						"dataType"      => "String",
						"required"      => true,
						"allowMultiple" => false
					);
					break;
				case 'id':
					$swagger[] = array(
						"paramType"     => "path",
						"name"          => $param,
						"description"   => "Identifier of the resource to retrieve.",
						"dataType"      => "String",
						"required"      => true,
						"allowMultiple" => false
					);
					break;
				case 'ids':
					$swagger[] = array(
						"paramType"     => "query",
						"name"          => $param,
						"description"   => "Comma-delimited list of the identifiers of the resources to retrieve.",
						"dataType"      => "String",
						"required"      => false,
						"allowMultiple" => true
					);
					break;
				case 'filter':
					$swagger[] = array(
						"paramType"     => "query",
						"name"          => $param,
						"description"   => "SQL-like filter to limit the resources to retrieve.",
						"dataType"      => "String",
						"required"      => false,
						"allowMultiple" => false
					);
					break;
				case 'order':
					$swagger[] = array(
						"paramType"     => "query",
						"name"          => $param,
						"description"   => "SQL-like order containing field and direction for filter results.",
						"dataType"      => "String",
						"required"      => false,
						"allowMultiple" => true
					);
					break;
				case 'limit':
					$swagger[] = array(
						"paramType"     => "query",
						"name"          => $param,
						"description"   => "Set to limit the filter results.",
						"dataType"      => "int",
						"required"      => false,
						"allowMultiple" => false
					);
					break;
				case 'include_count':
					$swagger[] = array(
						"paramType"     => "query",
						"name"          => $param,
						"description"   => "Include the total number of filter results.",
						"dataType"      => "boolean",
						"required"      => false,
						"allowMultiple" => false
					);
					break;
				case 'include_schema':
					$swagger[] = array(
						"paramType"     => "query",
						"name"          => $param,
						"description"   => "Include the schema of the table queried.",
						"dataType"      => "boolean",
						"required"      => false,
						"allowMultiple" => false
					);
					break;
				case 'fields':
					$swagger[] = array(
						"paramType"     => "query",
						"name"          => $param,
						"description"   => "Comma-delimited list of field names to retrieve for each record.",
						"dataType"      => "String",
						"required"      => false,
						"allowMultiple" => true
					);
					break;
				case 'related':
					$swagger[] = array(
						"paramType"     => "query",
						"name"          => $param,
						"description"   => "Comma-delimited list of related names to retrieve for each record.",
						"dataType"      => "string",
						"required"      => false,
						"allowMultiple" => true
					);
					break;
				case 'record':
					$swagger[] = array(
						"paramType"     => "body",
						"name"          => $param,
						"description"   => "Array of record properties.",
						"dataType"      => "array",
						"required"      => true,
						"allowMultiple" => true
					);
					break;
				case 'pkg':
					$swagger[] = array(
						"paramType"     => "query",
						"name"          => $param,
						"description"   => "Package the contents of the application for export.",
						"dataType"      => "boolean",
						"required"      => false,
						"allowMultiple" => false
					);
					$swagger[] = array(
						"paramType"     => "query",
						"name"          => "include_files",
						"description"   => "Include the files of the application in the package.",
						"dataType"      => "boolean",
						"required"      => false,
						"allowMultiple" => false
					);
					$swagger[] = array(
						"paramType"     => "query",
						"name"          => "include_services",
						"description"   => "Include related services of the application in the package.",
						"dataType"      => "boolean",
						"required"      => false,
						"allowMultiple" => false
					);
					$swagger[] = array(
						"paramType"     => "query",
						"name"          => "include_schema",
						"description"   => "Include related db schema of the application in the package.",
						"dataType"      => "boolean",
						"required"      => false,
						"allowMultiple" => false
					);
					break;
				case 'url':
					$swagger[] = array(
						"paramType"     => "query",
						"name"          => $param,
						"description"   => "URL path of the package file to upload.",
						"dataType"      => "string",
						"required"      => false,
						"allowMultiple" => false
					);
					break;
			}
		}

		return $swagger;
	}

	/**
	 * Define dynamic swagger output for each service resource.
	 * Currently used only for the System service, but maybe others later.
	 *
	 * @param string $service
	 * @param string $resource
	 * @param string $label
	 * @param string $plural
	 *
	 * @return array
	 */
	public static function apisPerResource( $service, $resource, $label = '', $plural = '' )
	{
		if ( empty( $label ) )
		{
			$label = Utilities::labelize( $resource );
		}
		if ( empty( $plural ) )
		{
			$plural = Utilities::pluralize( $label );
		}
		$className = str_replace( ' ', '', $label );
		$classPlural = str_replace( ' ', '', $plural );
		$apis = array(
			array(
				'path'        => '/' . $service . '/' . $resource,
				'description' => 'Operations for ' . ucfirst( $plural ) . ' administration.',
				'operations'  => array(
					array(
						"httpMethod"     => "GET",
						"summary"        => "Retrieve multiple $plural",
						"notes"          => "Use the 'ids' or 'filter' parameter to limit resources that are returned. " .
					                        "Use the 'fields' and 'related' parameters to limit properties returned for each resource. " .
					                        "By default, all fields and no relations are returned for all resources.",
						"responseClass"  => $classPlural,
						"nickname"       => "get" . $classPlural,
						"parameters"     => static::getParameters(
							array(
								 'ids',
								 'filter',
								 'limit',
								 'offset',
								 'order',
								 'include_count',
								 'include_schema',
								 'fields',
								 'related'
							)
						),
						"errorResponses" => static::getErrors( array( ErrorCodes::BAD_REQUEST ) )
					),
					array(
						"httpMethod"     => "POST",
						"summary"        => "Create one or more $plural",
						"notes"          => "Post data should be a single $className or $classPlural, an array of $className (shown). " .
					                        "By default, only the id property of the $className is returned on success, " .
											"use 'fields' and 'related' to return more info.",
						"responseClass"  => $classPlural,
						"nickname"       => "create" . $classPlural,
						"parameters"     => static::getParameters( array( 'fields', 'related', ( ( 'app' == $resource ) ? 'url' : '' ) ) ),
						"errorResponses" => static::getErrors( array( ErrorCodes::BAD_REQUEST ) )
					),
					array(
						"httpMethod"     => "PUT",
						"summary"        => "Update one or more $plural",
						"notes"          => "Post data should be a single $className or $classPlural, an array of $className (shown). " .
											"By default, only the id property of the $className is returned on success, " .
											"use 'fields' and 'related' to return more info.",
						"responseClass"  => $classPlural,
						"nickname"       => "update" . $classPlural,
						"parameters"     => static::getParameters( array( 'fields', 'related' ) ),
						"errorResponses" => static::getErrors( array( ErrorCodes::BAD_REQUEST ) )
					),
					array(
						"httpMethod"     => "DELETE",
						"summary"        => "Delete one or more $plural",
						"notes"          => "Post data should be a single $className or $classPlural, an array of $className (shown). " .
											"By default, only the id property of the $className is returned on success, " .
											"use 'fields' and 'related' to return more info.",
						"responseClass"  => $classPlural,
						"nickname"       => "delete" . $classPlural,
						"parameters"     => static::getParameters( array( 'fields', 'related' ) ),
						"errorResponses" => static::getErrors( array( ErrorCodes::BAD_REQUEST ) )
					),
				)
			),
			array(
				'path'        => '/' . $service . '/' . $resource . '/{id}',
				'description' => 'Operations for single ' . ucfirst( $label ) . ' administration.',
				'operations'  => array(
					array(
						"httpMethod"     => "GET",
						"summary"        => "Retrieve one $label by identifier",
						"notes"          => "Use the 'fields' and/or 'related' parameter to limit properties that are returned.",
						"responseClass"  => $className,
						"nickname"       => "get" . $className,
						"parameters"     => static::getParameters( array( 'id', 'fields', 'related', ( ( 'app' == $resource ) ? 'pkg' : '' ) ) ),
						"errorResponses" => static::getErrors( array( ErrorCodes::BAD_REQUEST ) )
					),
					array(
						"httpMethod"     => "PUT",
						"summary"        => "Update one $label by identifier",
						"notes"          => "Post data should be an array of fields for a single $resource",
						"responseClass"  => "array",
						"nickname"       => "update" . $className,
						"parameters"     => static::getParameters( array( 'id', 'fields', 'related' ) ),
						"errorResponses" => static::getErrors( array( ErrorCodes::BAD_REQUEST ) )
					),
					array(
						"httpMethod"     => "DELETE",
						"summary"        => "Delete one $label by identifier",
						"notes"          => "Use the 'fields' and/or 'related' parameter to return properties that are deleted.",
						"responseClass"  => "array",
						"nickname"       => "delete" . $className,
						"parameters"     => static::getParameters( array( 'id', 'fields', 'related' ) ),
						"errorResponses" => static::getErrors( array( ErrorCodes::BAD_REQUEST ) )
					),
				)
			),
		);

		return $apis;
	}

	/**
	 * Define dynamic swagger output for each service resource.
	 * Currently used only for the System service, but maybe others later.
	 *
	 * @param string $service
	 * @param string $resource
	 * @param string $label
	 * @param string $plural
	 *
	 * @return array
	 */
	public static function modelsPerResource( $service, $resource, $label = '', $plural = '' )
	{
		if ( empty( $label ) )
		{
			$label = Utilities::labelize( $resource );
		}
		if ( empty( $plural ) )
		{
			$plural = Utilities::pluralize( $label );
		}
		$className = str_replace( ' ', '', $label );
		$classPlural = str_replace( ' ', '', $plural );
		$models = array(
			$classPlural => array(
				"id"         => $classPlural,
				"properties" => array(
					"record" => array(
						"type"        => "array",
						"items"       => array( '$ref' => $className ),
						"description" => "Array of system records of the given resource."
					),
				)
			),
			$className  => array(
				"id"         => $className,
				"properties" => array(
					"created_date"        => array(
						"type"        => "string",
						"description" => "Date this record was created."
					),
					"created_by_id"       => array(
						"type"        => "integer",
						"description" => "User Id of who created this record."
					),
					"last_modified_date"  => array(
						"type"        => "string",
						"description" => "Date this record was last modified."
					),
					"last_modified_by_id" => array(
						"type"        => "integer",
						"description" => "User Id of who last modified this record."
					),
					"name"                => array(
						"type"        => "string",
						"description" => "Displayable name of this resource."
					),
					"api_name"            => array(
						"type"        => "string",
						"description" => "Name of the resource to use in API transactions."
					),
					"description"         => array(
						"type"        => "string",
						"description" => "Description of this resource."
					),
					"is_active"           => array(
						"type"        => "boolean",
						"description" => "Is this system resource active for use."
					),
				)
			),
		);

		return $models;
	}

}
