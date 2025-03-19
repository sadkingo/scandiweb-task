import { ApolloClient, InMemoryCache, HttpLink, from, WatchQueryFetchPolicy, FetchPolicy } from '@apollo/client';
import { onError } from '@apollo/client/link/error';
import { API_CONFIG, CACHE_CONFIG } from '@/config';

// Error handling link to intercept and handle GraphQL errors
const errorLink = onError(({graphQLErrors, networkError}) => {
    if (graphQLErrors) {
        graphQLErrors.forEach(({message, locations, path}) => {
            console.error(
                `[GraphQL error]: Message: ${message}, Location: ${locations}, Path: ${path}`,
            );
        });
    }
    if (networkError) {
        console.error(`[Network error]: ${networkError}`);
    }
});

// Create an HTTP link to the GraphQL endpoint
const httpLink = new HttpLink({
    uri: API_CONFIG.GRAPHQL_ENDPOINT,
    headers: {
        'Content-Type': 'application/json',
    },
});

// Combine the links
const link = from([errorLink, httpLink]);

// Configure cache with type policies for proper normalization
const cache = new InMemoryCache({
    typePolicies: {
        Product: {
            keyFields: ['id'],
            fields: {
                // Custom merge function for product attributes
                attributes: {
                    merge(_existing = [], incoming) {
                        return incoming;
                    },
                },
            },
        },
    },
});

// Create the Apollo Client instance
const client = new ApolloClient({
    link,
    cache,
    connectToDevTools: process.env.NODE_ENV === 'development',
    defaultOptions: {
        watchQuery: {
            fetchPolicy: CACHE_CONFIG.DEFAULT_CACHE_POLICY as WatchQueryFetchPolicy,
            errorPolicy: 'all',
            notifyOnNetworkStatusChange: true,
        },
        query: {
            fetchPolicy: CACHE_CONFIG.DEFAULT_CACHE_POLICY as FetchPolicy,
            errorPolicy: 'all',
        },
        mutate: {
            errorPolicy: 'all',
        },
    },
});

export default client;