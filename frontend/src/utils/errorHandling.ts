/**
 * Error handling utilities
 * Centralizes error handling logic for the application
 */

import { ApolloError } from '@apollo/client';
import { ERROR_MESSAGES } from '@/config';

/**
 * Interface for standardized error response
 */
export interface ErrorResponse {
  message: string;
  code?: string;
  technical?: string;
  isNetworkError?: boolean;
}

/**
 * Process Apollo errors into a standardized format
 * @param error - Apollo error object
 * @returns Standardized error response
 */
export const processApolloError = (error?: ApolloError): ErrorResponse => {
  if (!error) {
    return {message: ''};
  }

  // Handle network errors
  if (error.networkError) {
    return {
      message: ERROR_MESSAGES.NETWORK_ERROR,
      technical: error.networkError.message,
      isNetworkError: true,
      code: 'NETWORK_ERROR',
    };
  }

  // Handle GraphQL errors
  if (error.graphQLErrors?.length) {
    const graphQLError = error.graphQLErrors[0];
    return {
      message: `GraphQL error: ${graphQLError.message}`,
      code: graphQLError.extensions?.code as string || 'GRAPHQL_ERROR',
      technical: JSON.stringify(graphQLError.extensions || {}),
    };
  }

  // Fallback for other errors
  return {
    message: error.message || ERROR_MESSAGES.GENERAL_ERROR,
    code: 'UNKNOWN_ERROR',
  };
};

/**
 * Process generic errors into a standardized format
 * @param error - Any error object
 * @returns Standardized error response
 */
export const processGenericError = (error: unknown): ErrorResponse => {
  if (error instanceof Error) {
    return {
      message: error.message || ERROR_MESSAGES.GENERAL_ERROR,
      technical: error.stack,
      code: 'JS_ERROR',
    };
  }

  return {
    message: ERROR_MESSAGES.GENERAL_ERROR,
    technical: String(error),
    code: 'UNKNOWN_ERROR',
  };
};

/**
 * Log error to console with additional context
 * @param error - Error to log
 * @param context - Additional context information
 */
export const logError = (error: unknown, context?: Record<string, unknown>): void => {
  console.error('Application error:', error);

  if (context) {
    console.error('Error context:', context);
  }

  if (error instanceof Error && error.stack) {
    console.error('Stack trace:', error.stack);
  }
};