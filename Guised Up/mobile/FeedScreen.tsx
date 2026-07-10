import React, {useEffect, useMemo, useState} from 'react';
import {
  ActivityIndicator,
  FlatList,
  RefreshControl,
  StyleSheet,
  Text,
  TextInput,
  TouchableOpacity,
  View,
  Animated,
} from 'react-native';

const API_BASE_URL = 'http://127.0.0.1:8000/api';
const TOKEN = 'demo-token';

const theme = {
  bg: '#f4f7fb',
  card: '#ffffff',
  primary: '#3652AD',
  accent: '#F7B267',
  text: '#1f2a44',
  muted: '#6f7a95',
  border: '#e6ebf5',
};

const formatRelativeTime = (value: string) => {
  const createdAt = new Date(value);
  const diffMs = Date.now() - createdAt.getTime();
  const diffHours = Math.max(1, Math.round(diffMs / (1000 * 60 * 60)));
  return `${diffHours}h ago`;
};

const FeedScreen = () => {
  const [posts, setPosts] = useState<any[]>([]);
  const [page, setPage] = useState(1);
  const [loading, setLoading] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const [searchResults, setSearchResults] = useState<any[]>([]);
  const [searching, setSearching] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const [hasMore, setHasMore] = useState(true);
  const [animatedValue] = useState(new Animated.Value(1));

  const fetchFeed = async (nextPage = 1) => {
    if (loading) return;
    setLoading(true);
    setError(null);

    try {
      const response = await fetch(`${API_BASE_URL}/feed?page=${nextPage}`, {
        headers: {Authorization: `Bearer ${TOKEN}`},
      });
      const json = await response.json();
      const nextItems = json.data ?? [];
      setPosts(prev => (nextPage === 1 ? nextItems : [...prev, ...nextItems]));
      setHasMore(nextItems.length === 20);
    } catch (e) {
      setError('We could not refresh the feed.');
    } finally {
      setLoading(false);
    }
  };

  const searchPosts = async () => {
    if (!searchQuery.trim()) {
      setSearchResults([]);
      setSearching(false);
      return;
    }

    setSearching(true);
    setLoading(true);
    try {
      const response = await fetch(`${API_BASE_URL}/search?q=${encodeURIComponent(searchQuery)}`, {
        headers: {Authorization: `Bearer ${TOKEN}`},
      });
      const json = await response.json();
      setSearchResults(json);
    } catch (e) {
      setError('The search request failed.');
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchFeed(1);
  }, []);

  const listData = useMemo(() => (searching ? searchResults : posts), [searching, searchResults, posts]);

  const animateReaction = () => {
    Animated.sequence([
      Animated.timing(animatedValue, {toValue: 1.2, duration: 120, useNativeDriver: true}),
      Animated.timing(animatedValue, {toValue: 1, duration: 120, useNativeDriver: true}),
    ]).start();
  };

  const renderPost = ({item}: {item: any}) => (
    <View style={styles.card}>
      <View style={styles.cardHeader}>
        <View style={styles.avatarCircle}>
          <Text style={styles.avatarText}>{item.user?.name?.slice(0, 2).toUpperCase() ?? 'U'}</Text>
        </View>
        <View style={styles.metaWrap}>
          <Text style={styles.userName}>{item.user?.name ?? 'Anonymous'}</Text>
          <Text style={styles.timeText}>{formatRelativeTime(item.created_at)}</Text>
        </View>
      </View>
      <Text style={styles.postText}>{item.text}</Text>
      <TouchableOpacity style={styles.reactionButton} onPress={animateReaction}>
        <Animated.Text style={[styles.reactionText, {transform: [{scale: animatedValue}]}]}>♡ React</Animated.Text>
      </TouchableOpacity>
    </View>
  );

  return (
    <View style={styles.container}>
      <View style={styles.headerBar}>
        <Text style={styles.title}>Real Connections</Text>
        <Text style={styles.subtitle}>Personalized, meaningful updates</Text>
      </View>
      <View style={styles.searchBox}>
        <TextInput
          placeholder="Search posts"
          value={searchQuery}
          onChangeText={setSearchQuery}
          onSubmitEditing={searchPosts}
          style={styles.input}
          placeholderTextColor={theme.muted}
        />
        {searching ? (
          <TouchableOpacity onPress={() => {setSearching(false); setSearchResults([]); setSearchQuery('');}}>
            <Text style={styles.clearText}>Clear</Text>
          </TouchableOpacity>
        ) : null}
      </View>
      {loading && !posts.length ? (
        <View style={styles.centerState}><ActivityIndicator size="large" color={theme.primary} /></View>
      ) : null}
      {error ? (
        <View style={styles.centerState}>
          <Text style={styles.emptyText}>{error}</Text>
          <TouchableOpacity style={styles.retryButton} onPress={() => fetchFeed(1)}><Text style={styles.retryText}>Retry</Text></TouchableOpacity>
        </View>
      ) : null}
      {!loading && !error && !listData.length ? (
        <View style={styles.centerState}><Text style={styles.emptyText}>Your feed is quiet right now. Try searching or posting something new.</Text></View>
      ) : null}
      <FlatList
        data={listData}
        keyExtractor={item => String(item.id)}
        renderItem={renderPost}
        refreshControl={<RefreshControl refreshing={loading} onRefresh={() => fetchFeed(1)} tintColor={theme.primary} />}
        onEndReached={() => {
          if (!searching && hasMore) {
            const nextPage = page + 1;
            setPage(nextPage);
            fetchFeed(nextPage);
          }
        }}
        onEndReachedThreshold={0.2}
        contentContainerStyle={styles.listContent}
      />
    </View>
  );
};

const styles = StyleSheet.create({
  container: {flex: 1, backgroundColor: theme.bg},
  headerBar: {paddingHorizontal: 20, paddingTop: 24, paddingBottom: 12},
  title: {fontSize: 24, fontWeight: '700', color: theme.text},
  subtitle: {fontSize: 14, color: theme.muted, marginTop: 4},
  searchBox: {flexDirection: 'row', alignItems: 'center', marginHorizontal: 16, marginBottom: 8, paddingHorizontal: 12, paddingVertical: 8, backgroundColor: theme.card, borderRadius: 16, shadowColor: '#000', shadowOpacity: 0.06, shadowRadius: 8, shadowOffset: {width: 0, height: 2}},
  input: {flex: 1, color: theme.text, fontSize: 15},
  clearText: {color: theme.primary, fontWeight: '600', marginLeft: 8},
  listContent: {paddingHorizontal: 16, paddingBottom: 24},
  card: {backgroundColor: theme.card, borderRadius: 18, padding: 16, marginBottom: 14, borderWidth: 1, borderColor: theme.border, shadowColor: '#000', shadowOpacity: 0.06, shadowRadius: 10, shadowOffset: {width: 0, height: 3}},
  cardHeader: {flexDirection: 'row', alignItems: 'center', marginBottom: 12},
  avatarCircle: {width: 42, height: 42, borderRadius: 21, backgroundColor: theme.primary, alignItems: 'center', justifyContent: 'center'},
  avatarText: {color: '#fff', fontWeight: '700'},
  metaWrap: {marginLeft: 10},
  userName: {fontSize: 15, fontWeight: '600', color: theme.text},
  timeText: {fontSize: 12, color: theme.muted, marginTop: 2},
  postText: {fontSize: 15, lineHeight: 22, color: theme.text},
  reactionButton: {marginTop: 14, alignSelf: 'flex-start', paddingVertical: 8, paddingHorizontal: 12, borderRadius: 999, backgroundColor: '#fef2e7'},
  reactionText: {fontSize: 14, fontWeight: '600', color: theme.accent},
  centerState: {flex: 1, justifyContent: 'center', alignItems: 'center', padding: 24},
  emptyText: {fontSize: 15, color: theme.muted, textAlign: 'center'},
  retryButton: {marginTop: 12, backgroundColor: theme.primary, paddingVertical: 10, paddingHorizontal: 16, borderRadius: 999},
  retryText: {color: '#fff', fontWeight: '600'},
});

export default FeedScreen;
