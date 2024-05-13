from sklearn.metrics.pairwise import cosine_similarity
import numpy as np
import json
import sys


# Function to compute recommendations
def recommend_events():
    # Supposing user_profiles is a dictionary containing user profiles and their associated events
    # Format: {'user1': ['event1', 'event2'], 'user2': ['event1', 'event3', 'event4', 'event6'], ...}
    if (len(sys.argv) > 0):
        user_profiles_serialized = sys.argv[1]
        #print("from python serri",user_profiles_serialized)
        user_data = json.loads(user_profiles_serialized)
        #print("from python data",user_data)
        user_profiles = user_data['userProfiles']
        user = user_data['userId']
    # Create a set of all events across all user profiles
    all_events = set()
    for events in user_profiles.values():
        all_events.update(events)

    # Calculate similarity between user profiles
    similarities = {}
    for user1, profile1 in user_profiles.items():
        similarities[user1] = {}
        for user2, profile2 in user_profiles.items():
            if user1 != user2:
                # Create binary vectors to represent user profiles
                vector1 = np.array([1 if event in profile1 else 0 for event in all_events])
                vector2 = np.array([1 if event in profile2 else 0 for event in all_events])
                similarity = cosine_similarity([vector1], [vector2])[0][0]
                similarities[user1][user2] = similarity

    # Use similarities to recommend events
    recommendations = set()
    for other_user, similarity in sorted(similarities[user].items(), key=lambda x: x[1], reverse=True):
        if similarity > 0:  # Similar user
            recommendations.update(set(user_profiles[other_user]) - set(user_profiles[user]))

    # Filter out events already participated or favored by the target user
    recommendations -= set(user_profiles[user])
     
    return json.dumps({'recommendations': list(recommendations)})
print(recommend_events())
""""
if (len(sys.argv) > 0):
    user_profiles_serialized = sys.argv[1]
    print("from python serri",user_profiles_serialized)
    user_data = json.loads(user_profiles_serialized)
    print("from python data",user_data)
    user_profiles = user_data['userProfiles']
    user = user_data['userId']
    recommend_events(user_profiles, user)


# Example usage
user_profiles = {#users
    'x': ['event1', 'event2'], # union participer et favorie
    'y': ['event1', 'event3', 'event4', 'event6']
}
user_x_recommendations = recommend_events(user_profiles, 'x')
user_y_recommendations = recommend_events(user_profiles, 'y')

# Print recommendations for users x and y
print("Recommendations for user x:", user_x_recommendations)
print("Recommendations for user y:", user_y_recommendations)
"""