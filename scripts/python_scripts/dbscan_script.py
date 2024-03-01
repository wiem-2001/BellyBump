# Import necessary libraries
from sklearn.cluster import DBSCAN
import numpy as np

# Function to perform DBSCAN clustering on user ages
def cluster_users_by_age(user_ages):
    # Convert user ages to numpy array
    ages_array = np.array(user_ages).reshape(-1, 1)  # Reshape to a column vector
    
    # Define DBSCAN parameters (you may need to adjust these)
    eps = 5  # Epsilon neighborhood size
    min_samples = 5  # Minimum number of samples in a neighborhood
    
    # Initialize DBSCAN object
    dbscan = DBSCAN(eps=eps, min_samples=min_samples)
    
    # Perform clustering
    labels = dbscan.fit_predict(ages_array)
    
    # Assign cluster labels to users
    clustered_users = {}
    for i, label in enumerate(labels):
        if label not in clustered_users:
            clustered_users[label] = []
        clustered_users[label].append(user_ages[i])
    
    return clustered_users

# Sample user ages retrieved from Symfony application (replace with actual data)
user_ages = [20, 25, 27, 30, 33, 35, 40, 42, 45, 50]


# Perform DBSCAN clustering
clustered_users = cluster_users_by_age(user_ages)

# Print clustered users
for cluster_label, ages in clustered_users.items():
    print(f"Cluster {cluster_label}: {ages}")