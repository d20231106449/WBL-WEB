const supabaseUrl = import.meta.env.VITE_SUPABASE_URL?.trim();
const supabaseKey = (
    import.meta.env.VITE_SUPABASE_ANON_KEY
    || import.meta.env.VITE_SUPABASE_PUBLISHABLE_KEY
)?.trim();

let supabaseClientPromise;

export function getSupabaseClient() {
    if (!supabaseUrl || !supabaseKey) {
        const message = 'Supabase environment variables are missing.';
        console.error(message);
        throw new Error(message);
    }

    supabaseClientPromise ??= import(
        /* @vite-ignore */ 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2/+esm'
    ).then(({ createClient }) => createClient(supabaseUrl, supabaseKey, {
        auth: {
            autoRefreshToken: false,
            detectSessionInUrl: false,
            persistSession: false,
        },
    }));

    return supabaseClientPromise;
}
